<?php
namespace jens1o\hitbox\token;

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

    // TODO: Add useful methods when implementing them right

}