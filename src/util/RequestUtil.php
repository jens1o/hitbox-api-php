<?php
namespace jens1o\smashcast\util;

use GuzzleHttp\Exception\{ClientException, GuzzleException};
use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\SmashcastApiException;

/**
 * Manages requests (moved to here so static models can use it too)
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
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
     * @throws SmashcastApiException
     */
    public static function doRequest(string $method, string $path, array $parameters = [], bool $needsAuthToken = null) {
        $needsAuthToken = $needsAuthToken ?? false;

        $authToken = SmashcastApi::getUserAuthToken();
        $appendAuthToken = $parameters['appendAuthToken'] ?? false;
        $noAuthToken = $parameters['noAuthToken'] ?? false;

        if($authToken !== null && !$noAuthToken) {
            if($appendAuthToken) {
                $path .= '/' . $authToken->getToken();
            } else {
                $parameters['query']['authToken'] = $authToken->getToken();
            }
        } elseif($needsAuthToken) {
            throw new \BadMethodCallException('No auth token set(or it was overwritten by `noAuthToken`) but the wanted resource needs one! Set the token with SmashcastApi::setAuthToken($authToken)!');
        }

        unset($parameters['appendAuthToken']);
        unset($parameters['noAuthToken']);

        try {
            self::$lastRequest = SmashcastApi::getClient()->request($method, $path, $parameters);
        } catch(GuzzleException $e) {
            /*
                A little note on this if-statement:
                Yeah, I know I can create a new catch-block, but I want that only one exception will be rethrown.
            */
            if($e instanceof ClientException && $e->getResponse()->getStatusCode() === 400) {
                // try to encode
                $response = @json_decode($e->getResponse()->getBody());
                if(json_last_error() === JSON_ERROR_NONE && isset($response->error_msg)) {
                    switch($response->error_msg) {
                        case 'invalid_app':
                            throw new SmashcastApiException('The app you defined with HitboxApi#setAppName is not registered at hitbox!');
                    }
                }
            }
            // rethrow exception
            throw new SmashcastApiException('Fetching data from the smashcast api failed!', 0, $e);
            return null;
        }

        return json_decode(self::$lastRequest->getBody());
    }

    /**
     * Returns the response of the latest request
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public static function getLastRequest() {
        return self::$lastRequest;
    }

}