<?php
namespace jens1o\hitbox\token;

use jens1o\hitbox\exception\{HitboxApiException, HitboxAuthException};
use jens1o\hitbox\util\{HttpMethod, RequestUtil};

/**
 * Provides useful methods for auth tokens and holds them
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox
 * @subpackage token
 */
class HitboxAuthToken {

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
     * @see HitboxAuthToken::getToken()
     * @return string
     */
    public function __toString() {
        return $this->token;
    }

    /**
     * Returns an auth token by logging in with credentials. Returns null when an error occurred
     *
     * @return Token|null
     * @throws HitboxAuthException
     */
    public static function getTokenByLogin(string $userName, string $password, ?string $app = null): ?HitboxAuthToken {
        $app = $app ?? 'desktop';

        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, '/auth/token', [
                'json' => [
                    'login' => $userName,
                    'pass' => $password,
                    'app' => $app
                ],
                'noAuthToken' => true
            ]);
        } catch(HitboxApiException $e) {
            throw new HitboxAuthException('Cannot authenticate with hitbox api! Check username or password.', 0, $e);
            return null;
        }

        if(isset($request->authToken) && $request->authToken) {
            return new self((string) $request->authToken);
        }
        return null;
    }

    // TODO: Add useful methods when implementing them right

}