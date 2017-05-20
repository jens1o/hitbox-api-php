<?php
namespace jens1o\smashcast\model;

/**
 * Implementation for models which can update their properties.
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage model
 */
interface IUpdatable {
    /**
     * Updates the options of this model
     */
    public function update(array $updateParts);

    /**
     * Validates wether the user provided invalid data to update
     */
    public function validateUpdate(array $updateParts): bool;
}