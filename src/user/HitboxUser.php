<?php
namespace jens1o\hitbox\user;

use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\exception\{HitboxApiException, HitboxAuthException};
use jens1o\hitbox\model\AbstractModel;
use jens1o\hitbox\util\{HttpMethod, RequestUtil};

/**
 * Represents a Hitbox User that can access to channels and media
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox
 * @subpackage user
 */
class HitboxUser extends AbstractModel {

    /**
     * @inheritDoc
     */
    public $appendAuthToken = false;

    /**
     * Creates a new User object.
     * **Warning!** This executes immediately a request to hitbox fetching all data when `$row` is not provided!
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
     * Returns the user object by username and password. This includes private information!
     *
     * @param   string  $userName   The username of the user that you want the info from
     * @param   string  $password   The password of the user
     * @param   string  $app        The type of app this is(defaults to 'desktop')
     * @return HitboxUser
     * @throws HitboxAuthException
     */
    public static function getUserByLogin(string $userName, string $password, string $app = 'desktop') {
        $request = null;
        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, '/auth/login', [
                'json' => [
                    'login' => $userName,
                    'pass' => $password,
                    'app' => $app
                ],
                'noAuthToken' => true
            ]);
        } catch(HitboxApiException $e) {
            throw new HitboxAuthException('Cannot authenticate with hitbox api! Check username or password.', 0, $e);
        }


        return new self(null, $request);
    }

    /**
     * Returns the user the token belongs to
     * @param   string  $token  The token to Check
     * @return HitboxUser
     * @throws HitboxApiException When the token is not connected to a user
     */
    public static function getUserByToken(string $token) {
        $userName = static::getUserNameByToken($token);

        if($userName === null) {
            throw new HitboxApiException('The auth token is not in use by somebody!');
        }

        return new self($userName, null);
    }

    /**
     * Returns to which username the token belongs to(returns null when not existing)
     *
     * @param   string  $token  The token to Check
     * @return string|null
     */
    public static function getUserNameByToken(string $token) {
        $request = RequestUtil::doRequest(HttpMethod::GET, 'userfromtoken/' . $token, ['noAuthToken' => true]);

        if(isset($request->user_name)) {
            return $request->user_name;
        }

        return null;
    }

}