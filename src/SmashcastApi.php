<?php
namespace jens1o\smashcast;

use GuzzleHttp\Client;
use jens1o\smashcast\token\SmashcastAuthToken;

/**
 * The main class for connecting to the api of smashcast
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o
 * @subpackage smashcast
 */
class SmashcastApi {

    /**
     * The base url for accessing the api
     */
    public const BASE_URL = 'https://api.smashcast.tv';

    /**
     * Where all the images are saved on
     */
    public const IMAGE_URL = 'https://edge.sf.hitbox.tv'; // TODO: Check before each release whether they changed the url, it will be changed!

    /**
     * Holds the version string of this handler
     */
    public const VERSION = '0.8.0 dev';

    /**
     * Holds the http client that connects to the api
     * @var Client
     */
    private static $client = null;

    /**
     * Holds the auth token for accessing private/more data (null while in startup)
     * @var SmashcastAuthToken|null
     */
    private static $authToken = null;

    /**
     * The app name this client uses
     * @var string
     */
    private static $appName = 'desktop';

    /**
     * The token for this app(required when using oauth things)
     * @var string|null
     */
    private static $appToken = null;

    /**
     * The secret for this app(required when using oauth things)
     * @var string|null
     */
    private static $appSecret = null;

    /**
     * Inits the client which will connect to Smashcast's api
     *
     * @param   string  $appName    The app name this client uses for requests. (default `desktop`)
     * @param   string  $appToken   The token for this app
     * @param   string  $appSecret  The secret for this app
     */
    public function __construct(?string $appName = null, ?string $appToken = null, ?string $appSecret = null) {
        static::setAppName($appName ?? 'desktop');
        static::setAppToken($appToken);
        static::setAppSecret($appSecret);
    }

    /**
     * Sets the app name this client uses. You should call this on startup!
     *
     * @param   string  $appName    The app name this client should use.
     */
    public static function setAppName(string $appName) {
        self::$appName = $appName;
    }

    /**
     * Gets the app name this client uses
     *
     * @return string
     */
    public static function getAppName(): string {
        return self::$appName;
    }

    /**
     * Sets the app token this clients used. Necessary when using oauth.
     *
     * @param   string|null     $appToken   The app token.
     */
    public static function setAppToken(?string $appToken = null) {
        self::$appToken = $appToken;
    }

    /**
     * Returns the app token this client uses
     *
     * @return string
     */
    public static function getAppToken(): ?string {
        return self::$appToken;
    }

    /**
     * Sets the app secret this clients used. Necessary when using oauth.
     *
     * @param   string|null     $appSecret   The app token.
     */
    public static function setAppSecret(?string $appSecret = null) {
        self::$appSecret = $appSecret;
    }

    /**
     * Returns the app secret this client uses
     *
     * @return string
     */
    public static function getAppSecret(): ?string {
        return self::$appSecret;
    }

    /**
     * Sets the auth token
     *
     * @param   SmashcastAuthToken  $token  The auth token you'd got from a previous request
     */
    public static function setUserAuthToken(SmashcastAuthToken $token) {
        self::$authToken = $token;
    }

    /**
     * Returns the auth token
     *
     * @return SmashcastAuthToken
     */
    public static function getUserAuthToken(): SmashcastAuthToken {
        if(!(self::$authToken instanceof SmashcastAuthToken)) {
            self::$authToken = new SmashcastAuthToken('');
        }

        return self::$authToken;
    }

    /**
     * Returns the client that should be used to connect to their api
     * @return Client
     */
    public static function getClient(): Client {
        if(self::$client === null) {
            self::$client = new Client([
                'base_uri' => static::BASE_URL,
                'timeout' => 7,
                'headers' => [
                    'User-Agent' => 'jens1o/smashcast-api-php v' . self::VERSION
                ]
            ]);
        }

        return self::$client;
    }

}