<?php
namespace jens1o\hitbox;

use GuzzleHttp\Client;
use jens1o\hitbox\token\HitboxAuthToken;

/**
 * The main class for connecting to the api of hitbox
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o
 * @subpackage hitbox
 */
class HitboxApi {

    /**
     * The base url for accessing the api
     */
    public const BASE_URL = 'https://api.hitbox.tv';

    /**
     * Where all the images are saved on
     */
    public const IMAGE_URL = 'https://edge.sf.hitbox.tv';

    /**
     * Holds the version string of this handler
     */
    public const VERSION = '0.0.1 dev';

    /**
     * Holds the http client that connects to the api
     * @var Client
     */
    private static $client = null;

    /**
     * Holds the auth token for accessing private/more data
     * @var string
     */
    private static $authToken = null;

    /**
     * Sets the auth token
     *
     * @param   HitboxAuthToken  $token  The auth token you'd got from a previous request
     */
    public static function setUserAuthToken(HitboxAuthToken $token) {
        self::$authToken = $token;
    }

    /**
     * Returns the auth token
     *
     * @return string
     */
    public static function getAuthToken() {
        return self::$authToken;
    }

    /**
     * Returns the client that should be used to connect to their api
     * @return Client
     */
    public static function getClient() {
        if(self::$client === null) {
            self::$client = new Client([
                'base_uri' => self::BASE_URL,
                'timeout' => 7,
                'User-Agent' => 'jens1o/hitbox-api-php v' . self::VERSION
            ]);
        }

        return self::$client;
    }

}