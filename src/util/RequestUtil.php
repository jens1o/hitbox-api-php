<?php
namespace jens1o\hitbox\util;

use GuzzleHttp\Exception\GuzzleException;
use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\exception\HitboxApiException;

/**
 * Manages requests (moved to here so static models can use it too)
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox
 * @subpackage util
 */
class RequestUtil {

    /**
     * Holds the last executed response
     * @var Psr\Http\Message\ResponseInterface
     */
    private static $lastRequest = null;

    /**
     * Executes the request and returns a json-decoded array
     *
     * @param   string      $method             With which http method it should request
     * @param   mixed[]     $parameters         Parameters for the request
     * @param   bool        $needsAuthToken     Wether this request **requires** an auth token.
     * @return mixed[]|null
     * @throws \BadMethodCallException When `$needsAuthToken` is true and no auth token was set
     * @throws HitboxApiException
     */
    public static function doRequest(string $method, string $path, array $parameters = [], bool $needsAuthToken = null) {
        $needsAuthToken = $needsAuthToken ?? false;

        $authToken = HitboxApi::getAuthToken()->getToken();
        $appendAuthToken = $parameters['appendAuthToken'] ?? false;
        $noAuthToken = $parameters['noAuthToken'] ?? false;

        if($authToken !== null && !$noAuthToken) {
            if($appendAuthToken) {
                $path .= '/' . $authToken;
            } else {
                $parameters['query']['authToken'] = $authToken;
            }
        } elseif($needsAuthToken) {
            throw new \BadMethodCallException('No auth token set(or it was overwritten by `noAuthToken`) but the wanted resource needs one! Set the token with HitboxApi::setAuthToken($authToken)!');
        }

        unset($parameters['appendAuthToken']);
        unset($parameters['noAuthToken']);

        try {
            self::$lastRequest = HitboxApi::getClient()->request($method, $path, $parameters);
        } catch(GuzzleException $e) {
            // rethrow exception
            throw new HitboxApiException('Fetching data from the hitbox api failed!', 0, $e);
            return null;
        }

        return json_decode(self::$lastRequest->getBody());
    }

    public static function getLastRequest() {
        return self::$lastRequest;
    }

}