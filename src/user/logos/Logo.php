<?php
namespace jens1o\hitbox\user\logos;

use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\util\HttpMethod;

/**
 * Represents a logo that can be downloaded or just refers to a link
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox\user
 * @subpackage logos
 */
class Logo {

    /**
     * 
     */
    private $url = null;

    /**
     * Creates a new logo representation
     *
     * @param   string  $url    The uri (without the host) the image is hosted on
     */
    public function __construct(string $url) {
        $this->url = $url;
    }

    public function download(string $location) {
        HitboxApi::getClient()
            ->request(HttpMethod::GET, $this->getPath())
            ->setResponseBody($location)
            ->send();
    }

    /**
     * Returns the path of this logos
     * @var string
     */
    public function getPath(): string {
        return HitboxApi::IMAGE_URL . $this->url;
    }

    /**
     * @see getPath()
     */
    public function __toString(): string {
        return $this->getPath();
    }

}