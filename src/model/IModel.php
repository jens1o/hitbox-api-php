<?php
namespace jens1o\hitbox\model;

/**
 * Represents a model
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\hitbox
 * @subpackage model
 */
interface IModel {

    /**
     * Creates a new model
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null);

    /**
     * Executes an api request
     */
    public function doRequest(string $method, string $path, array $parameters = [], bool $needsAuthToken = false);

    /**
     * Returns the data
     */
    public function __get($needle);

}