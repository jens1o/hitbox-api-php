<?php
namespace jens1o\smashcast\user;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\{SmashcastApiException, SmashcastAuthException};
use jens1o\smashcast\model\AbstractModel;
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
        if(!$this->exists()) return false;

        $request = $this->doRequest(HttpMethod::GET, '/user/checkVerifiedEmail/' . $this->data->user_name, ['noAuthToken' => true]);

        if(!isset($request->user->user_activated) || $request->user->user_activated == '0') {
            return false;
        }

        return true;
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

}