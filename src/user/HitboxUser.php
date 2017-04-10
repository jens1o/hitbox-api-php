<?php
namespace jens1o\hitbox\user;

use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\exception\{HitboxApiException, HitboxAuthException};
use jens1o\hitbox\model\AbstractModel;
use jens1o\hitbox\user\logos\LogoHandler;
use jens1o\hitbox\util\{HttpMethod, LogoSize, RequestUtil};

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
     * Holds the logos
     * @var LogosHandler
     */
    private $logosHandler = null;

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
        return ($this->data->user_id !== null);
    }

    /**
     * Returns the logohandler that manages the logos(or null when the user does not exist)
     *
     * @return LogosHandler|null
     */
    public function getLogos(): ?LogoHandler {
        if($this->logosHandler === null) {
            if(!$this->exists()) {
                throw new \BadMethodCallException('Cannot show logos on non-existing users!');
                return null;
            }
            $this->logosHandler = new LogoHandler([
                LogoSize::SMALL => $this->data->user_logo_small,
                LogoSize::DEFAULT => $this->data->user_logo
            ]);
        }

        return $this->logosHandler;
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
    public static function getUserByLogin(string $userName, string $password, ?string $app = null): HitboxUser {
        $app = $app ?? 'desktop';

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
     * @return HitboxUser|null
     * @throws HitboxApiException When the token is not connected to a user
     */
    public static function getUserByToken(string $token): ?HitboxUser {
        $userName = static::getUserNameByToken($token);

        if($userName === null) {
            throw new HitboxApiException('The auth token is not in use by somebody!');
            return null;
        }

        return new self($userName, null);
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