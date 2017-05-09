<?php
namespace jens1o\smashcast\util;

/**
 * Holds all (important) http methods used in this handler
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage util
 */
interface HttpMethod {

    /**
     * GET request
     */
    public const GET = 'GET';

    /**
     * POST request
     */
    public const POST = 'POST';

    /**
     * PUT request
     */
    public const PUT = 'PUT';

    /**
     * PATCH request
     */
    public const PATCH = 'PATCH';

    /**
     * DELETE request
     */
    public const DELETE = 'DELETE';
}