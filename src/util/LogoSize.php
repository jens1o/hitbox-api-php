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
     * 200px x 200px big
     */
    public const DEFAULT = 'default';

    /**
     * 50px x 50px big
     */
    public const SMALL = 'small';

}