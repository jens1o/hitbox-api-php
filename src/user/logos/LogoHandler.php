<?php
namespace jens1o\hitbox\user\logos;

use jens1o\hitbox\util\LogoSize;

/**
 * Handles the logos
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox\user
 * @subpackage logos
 */
class LogoHandler {

    /**
     * Holds the links and sizes to the images
     * @var string[][]
     */
    private $images = [];

    /**
     * Holds a list of initiated logos
     * @var Logo[]
     */
    private static $initiatedLogos = [];

    /**
     * Inits the logo handler
     *
     * @param   string[][]  $images     The images that this class holds
     */
    public function __construct(array $images) {
        $this->images = $images;
    }

    /**
     * Returns the logo that fits the given size, or null when it wasn't found
     *
     * @param   string  $size   Wether `small` or `normal`
     * @throws \InvalidArgumentException
     */
    public function getLogo(string $size): ?Logo {
        switch($size) {
            case LogoSize::SMALL:
            case LogoSize::DEFAULT:
                break;
            default:
                throw new \InvalidArgumentException('The size neither `small` or `normal`!');
        }

        $location = static::$initiatedLogos[$this->images[$size]];

        if(!isset($location)) {
            $location = new Logo($this->images[$size]);
        }

        return $location;
    }

}