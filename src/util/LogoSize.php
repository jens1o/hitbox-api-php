<?php
namespace jens1o\smashcast\util;

/**
 * Pseudo-ENUM which holds the logo sizes
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage util
 */
interface LogoSize {

    /**
     * 200 x 200
     */
    public const DEFAULT = 'default';

    /**
     * 50 x 50
     */
    public const SMALL = 'small';

}