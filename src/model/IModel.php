<?php
namespace jens1o\smashcast\model;

/**
 * Represents a model
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage model
 */
interface IModel extends IUpdatable {

    /**
     * Creates a new model
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null);

    /**
     * Returns the data
     */
    public function __get($needle);

    /**
     * Returns wether this Model does exist
     *
     * @return bool
     */
    public function exists(): bool;
}