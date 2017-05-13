<?php
namespace jens1o\smashcast\oauth;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\SmashcastApiException;
use jens1o\smashcast\util\{HttpMethod, RequestUtil};

/**
 * Manages oauth-related requests
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage oauth
 */
class SmashcastOauthHandler {

    /**
     * Does the first job. Redirects to the Smashcast OAuth authentication page.
     * Note: You can do this by yourself! This is just existing so it makes starting easier(it just builds an url and redirects towards it)
     *
     * @param   bool            $forceAuth  Wether the authentication must be refreshed in case it exists
     * @param   string|null     $state      You can send a string to Smashcast, and you'll get this back. **You should save this and compare later!**
     * @throws \BadMethodCallException When an app token has not been set
     */
    public static function init(bool $forceAuth, ?string $state = null) {
        $appToken = SmashcastApi::getAppToken();

        if($appToken === null) {
            throw new \BadMethodCallException('You didn\'t set any app token, but this is needed when dealing with oauth!');
            return;
        }

        $url = 'https://api.smashcast.tv/oauth/login?app_token=' . $appToken;

        if($forceAuth) {
            $url .= '&force_auth=true';
        }
        if($state !== null) {
            $url .= '&state=' . $state;
        }

        // redirect temporary (so refreshing auth won't fail in any case)
        header('Location: ' . $url, true, 307);
        exit;
    }

    /**
     * Handles the step where the server needs to exchange the request token to an auth token.
     * The states are compared when both `$receivedState` and `$savedState` has been given.
     * Returns the auth token as a string when the process didn't failed.
     *
     * @param   string      $requestToken   The request token received with `$_GET['request_token']`
     * @param   string|null $receivedState  The state received from `$_GET['state']` (optional)
     * @param   string|null $savedState     The state saved from the session while starting the oauth authentication process
     * @return string|null
     * @throws SmashcastApiException When states don't match or the exchange process failed.
     */
    public static function getAuthTokenFromRequestToken(string $requestToken, ?string $receivedState = null, ?string $savedState = null): ?string {
        if($receivedState !== null && $savedState !== null) {
            if($receivedState !== $savedState) {
                throw new SmashcastApiException('States do not match!');
            }
        }
        // check first wether both the app token and the app secret has been defined
        $appToken = SmashcastApi::getAppToken();
        $appSecret = SmashcastApi::getAppSecret();
        if($appToken === null || $appSecret === null) {
            throw new SmashcastApiException('You must set an app token and an app secret in the ' . SmashcastApi::class . ' class to use this method!');
        }

        $hash = base64_encode($appToken . $appSecret);
        try {
            $response = RequestUtil::doRequest(HttpMethod::POST, 'oauth/exchange', [
                'json' => [
                    'request_token' => $requestToken,
                    'app_token' => $appToken,
                    'hash' => $hash
                ]
            ]);
        } catch(SmashcastApiException $e) {
            throw new SmashcastApiException('The authentication failed!', 0, $e);
            return null;
        }

        if(!empty($response->access_token)) {
            return $response->access_token;
        }

        return null;
    }

}