<?php
namespace jens1o\smashcast\model;

use jens1o\smashcast\exception\SmashcastApiException;

/**
 * Implementation for a model where the file contents can be downloaded
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage model
 */
interface IDownloadable {

    /**
     * Tries to download the logo to the given destination. Returns false on failure, true on success
     *
     * @param   string  $location   Where you want to have the file
     * @return bool
     * @throws SmashcastApiException on failure
     */
    public function download(string $location): bool;

    /**
     * Returns the stream of the downloaded model, null(and an exception) on failure
     *
     * @return string|null
     * @throws SmashcastApiException on failure
     */
    public function getStream(): ?string;

    /**
     * Returns the path of this model
     * @var string
     */
    public function getPath(): string;

    /**
     * @see IDownloadable#getPath()
     */
    public function __toString(): string;

}