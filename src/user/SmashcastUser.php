<?php
namespace jens1o\smashcast\user;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\{SmashcastApiException, SmashcastAuthException};
use jens1o\smashcast\model\AbstractModel;
use jens1o\smashcast\token\SmashcastAuthToken;
use jens1o\smashcast\user\logos\LogoHandler;
use jens1o\smashcast\util\{HttpMethod, LogoSize, RequestUtil};

/**
 * Represents a smashcast User that can access to channels and media
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage user
 */
class SmashcastUser extends AbstractModel {

    /**
     * Holds the logos
     * @var LogoHandler
     */
    private $logoHandler = null;

    /**
     * Holds the user auth token
     * @var SmashcastAuthToken
     */
    private $userAuthToken = null;

    /**
     * Creates a new User object.
     * **Warning!** This executes immediately a request to smashcast fetching all data when `$row` is not provided!
     *
     * @param   string|null     $identifier   The name of the user, can be `null` when `$row` is provided
     * @param   mixed[]|null    $row        All information about the user fetched from the api, can be `null` when `$userName` is provided
     * @throws \BadMethodCallException  when `$userName` and `$row` are null
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null) {
        if($row !== null) {
            // prefer $row when provided, so we don't need to call their api again
            $this->data = $row;
        } elseif($identifier !== null) {
            // call their api
            $this->data = $this->doRequest(HttpMethod::GET, 'user/' . $identifier);
        } else {
            throw new \BadMethodCallException('Try to call ' . self::class . ' with both arguments null. One must be given!');
        }
    }

    /**
     * Returns the id for this user
     *
     * @return int|null
     */
    public function getUserId(): ?int {
        return $this->data->user_id;
    }

    /**
     * Shorthand function to check wether this user exists
     *
     * @return bool
     */
    public function exists(): bool {
        return $this->data->user_id !== null;
    }

    /**
     * Returns the user auth token for this user, or null when it can't be created(because the api didn't provide an auth token)
     *
     * @return SmashcastAuthToken|null
     */
    public function getAuthToken(): ?SmashcastAuthToken {
        if($this->userAuthToken === null && isset($this->data->authToken)) {
            $this->userAuthToken = new SmashcastAuthToken($this->data->authToken);
        }

        return $this->userAuthToken;
    }

    /**
     * Returns the logohandler that manages the logos(or null when the user does not exist)
     *
     * @return LogoHandler|null
     */
    public function getLogos(): ?LogoHandler {
        if($this->logoHandler === null) {
            if(!$this->exists()) {
                throw new \BadMethodCallException('Cannot show logos on non-existing users!');
                return null;
            }

            $this->logoHandler = new LogoHandler([
                LogoSize::SMALL => $this->data->user_logo_small,
                LogoSize::DEFAULT => $this->data->user_logo
            ]);
        }

        return $this->logoHandler;
    }

    /**
     * Returns wether the user is live atm
     *
     * @return bool
     */
    public function isLive(): bool {
        // non existing user can stream? ;)
        if(!$this->exists()) return false;

        if(isset($this->data->media_is_live)) {
            // authorized api
            return (bool) $this->data->media_is_live;
        } elseif(isset($this->data->is_live)) {
            // public api
            return (bool) $this->data->is_live;
        }

        // not implemented, feel free to create a pr adding this!
        return false;
    }

    /**
     * Returns wether this user had validated their email
     *
     * @return bool
     * @throws SmashcastApiException
     */
    public function hasVerifiedEmail(): bool {
        // Smashcast's api handles this right, however we can save some time
        if(!$this->exists()) return false;

        $request = $this->doRequest(HttpMethod::GET, '/user/checkVerifiedEmail/' . $this->data->user_name, ['noAuthToken' => true]);

        if(!isset($request->user->user_activated) || $request->user->user_activated == '0') {
            return false;
        }

        return true;
    }

    /**
     * Returns true when the user has been created with an auth token
     *
     * @return bool
     */
    public function isAuthenticated(): bool {
        return isset($this->data->authToken);
    }

    /**
     * Updates the user, you **must not** specify `user_id` and `user_name`!
     *
     * @param   mixed[]     $updateParts    The parts you want to update
     * @return bool
     * @throws SmashcastApiException When validating failed
     */
    public function update(array $updateParts): bool {
        if(!$this->validateUpdate($updateParts)) return false;

        $userSettings = array_merge([
            'user_id' => $this->data->user_id,
            'user_name' => $this->data->user_name
        ], $updateParts);

        try {
            $this->doRequest(HttpMethod::PUT, 'user/' . $this->data->user_name, [
                'json' => $userSettings,
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            throw new SmashcastApiException('Updating an user object has failed!', 0, $e);
            return false;
        }

        // update the class itself
        $newData = array_merge($updateParts, (array) $this->data);

        // user_display_name must be omitted, so it's consistent with the api
        if(isset($updateParts['user_display_name'])) {
            $newData['user_name'] = $updateParts['user_display_name'];
            unset($newData['user_display_name']);
        }

        // finally: update
        $this->data = (object) $newData;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function validateUpdate(array $updateParts): bool {
        if(!$this->exists()) {
            throw new SmashcastApiException('To update an user, the user must exist ;)');
            return false;
        }

        if(!$this->isAuthenticated()) {
            throw new SmashcastApiException('You need to be authenticated to update user settings!');
            return false;
        }

        // check for forbidden, unchangeable fields
        $fields = ['user_id', 'user_name', 'authToken'];
        $failedFields = [];
        foreach($fields as $field) {
            if(array_key_exists($field, $updateParts)) {
                $failedFields[] = $field;
            }
        }
        if(count($failedFields)) {
            throw new SmashcastApiException('You MUST omit ' . implode(', ', $failedFields) . ' when trying to update an user object!');
            return false;
        }

        // check when updating user capitalization that it matches the username (both in lowercase)
        if(array_key_exists('user_display_name', $updateParts) && strtolower($updateParts['user_display_name']) !== strtolower($this->data->user_name)) {
            throw new SmashcastApiException('You may want to change the display name, but it **must** match the old name when both are lowered!');
        }

        $currentData = (array) $this->data;

        // check wether new fields have been invented
        $nonExistingFields = [];
        foreach($updateParts as $updatePart => $index) {
            // user_display_name is omitted/merged into user_name later and does not exist in the plain GET request
            if(!array_key_exists($updatePart, $currentData) && $updatePart !== 'user_display_name') {
                $nonExistingFields[] = $updatePart;
            }
        }
        if(count($nonExistingFields)) {
            throw new SmashcastApiException('When updating a user profile, you must not invent new, non-existing fields! (New fields: ' . implode(', ', $nonExistingFields) . ')');
        }

        return true;
    }

    /**
     * Returns true when this user has connected with twitter, false otherwise.
     *
     * @return bool
     */
    public function isConnectedWithTwitter(): bool {
        // undocumented thing, but thanks to Hitakashi for telling me...

        /*
            For anything needing this:
            ENDPOINT/social/twitter?user_name=blah&authToken=blah
        */
        try {
            $response = $this->doRequest(HttpMethod::GET, 'social/twitter', [
                'query' => [
                    'user_name' => $this->data->user_name
                ],
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            return false;
        }

        if(isset($response->message) && $response->message === 'connected') {
            return true;
        }

        return false;
    }

    /**
     * Returns the user object by username and password. This includes private information!
     *
     * @param   string  $userName   The username of the user that you want the info from
     * @param   string  $password   The password of the user
     * @param   string  $app        The type of app this is(defaults to 'desktop')
     * @return SmashcastUser|null
     * @throws SmashcastAuthException
     */
    public static function getUserByLogin(string $userName, string $password, ?string $app = null): ?SmashcastUser {
        $app = $app ?? SmashcastApi::getAppName();

        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, '/auth/login', [
                'json' => [
                    'login' => $userName,
                    'pass' => $password,
                    'app' => $app
                ],
                'noAuthToken' => true
            ]);
        } catch(SmashcastApiException $e) {
            throw new SmashcastAuthException('Cannot authenticate with smashcast api! Check username or password.', 0, $e);
            return null;
        }


        return new self(null, $request);
    }

    /**
     * Returns the user the token belongs to or null when it is not assigned to anyone
     *
     * @param   string  $token  The token to check
     * @param   string  $app    For which app this should login(defaults to `desktop`)
     * @return SmashcastUser|null
     * @throws SmashcastAuthException When the token is not connected to a user
     */
    public static function getUserByToken(string $token, string $app = null): ?SmashcastUser {
        $app = $app ?? SmashcastApi::getAppName();

        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, '/auth/login', [
                'json' => [
                    'app' => $app,
                    'authToken' => $token
                ],
                'noAuthToken' => true
            ]);
        } catch(SmashcastApiException $e) {
            throw new SmashcastAuthException('The token does not belong to any user!', 0, $e);
            return null;
        }

        return new self(null, $request);
    }

    /**
     * Returns to which username the token belongs to(returns null when not existing)
     *
     * @param   string  $token  The token to Check
     * @return string|null
     */
    public static function getUserNameByToken(string $token): ?string {
        $request = RequestUtil::doRequest(HttpMethod::GET, 'userfromtoken/' . $token, ['noAuthToken' => true]);

        if(isset($request->user_name)) {
            return $request->user_name;
        }

        return null;
    }

    /*
     NOTE:
     When updating the user profile, send `user_id` and `user_name` in any case. These might not be changed!
    */
}