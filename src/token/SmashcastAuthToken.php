<?php
namespace jens1o\smashcast\token;

use jens1o\smashcast\exception\{SmashcastApiException, SmashcastAuthException};
use jens1o\smashcast\util\{HttpMethod, RequestUtil};

/**
 * Provides useful methods for auth tokens and holds them
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage token
 */
class SmashcastAuthToken {

    /**
     * Holds the token string
     * @var string
     */
    private $token = null;

    /**
     * Creates a new auth token
     *
     * @param   string  $token  The auth token that this class is supposed to hold
     */
    public function __construct(string $token) {
        $this->token = $token;
    }

    /**
     * Returns the token
     *
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * Returns the token as plain text
     *
     * @see SmashcastAuthToken::getToken()
     * @return string
     */
    public function __toString() {
        return $this->token;
    }

    /**
     * Returns an auth token by logging in with credentials. Returns null when an error occurred
     *
     * @return Token|null
     * @throws SmashcastAuthException
     */
    public static function getTokenByLogin(string $userName, string $password, ?string $app = null): ?SmashcastAuthToken {
        $app = $app ?? SmashcastApi::getAppName();

        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, '/auth/token', [
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

        if(isset($request->authToken) && $request->authToken) {
            return new self((string) $request->authToken);
        }
        return null;
    }

    /**
     * Returns wether this token is valid by the provided app id
     *
     * @param   string  $appId  The app id
     * @return bool
     */
    public function isValid(string $appId): bool {
        try {
            $request = RequestUtil::doRequest(HttpMethod::GET, '/auth/valid/' . $appId . '?token=' . $this->getToken, [
                'query' => [
                    'token' => $this->getToken()
                ]
            ]);
        } catch(SmashcastApiException $e) {
            var_dump($e);
            return false;
        }

        if($request->error) return false; // should never happen, fallback

        return true;
    }

    // TODO: Add useful methods when implementing them right

}