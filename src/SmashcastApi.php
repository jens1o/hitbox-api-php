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
    public const IMAGE_URL = 'https://edge.sf.hitbox.tv'; // TODO: Check before each release wether they changed the url, it will be changed!

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
    public static function getUserAuthToken() {
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
                'User-Agent' => 'jens1o/smashcast-api-php v' . self::VERSION
            ]);
        }

        return self::$client;
    }

}