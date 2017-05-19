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
interface IModel {

    /**
     * Creates a new model
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null);

    /**
     * Returns the data
     */
    public function __get($needle);


    /**
     * Updates the options of this model
     */
    public function update(array $updateParts);

    /**
     * Validates wether the user provided invalid data to update
     */
    public function validateUpdate(array $updateParts): bool;
}