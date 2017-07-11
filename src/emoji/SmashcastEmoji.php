<?php
namespace jens1o\smashcast\emoji;

use jens1o\smashcast\model\IDownloadable;
use jens1o\smashcast\SmashcastApi;
use jens1o\util\HttpMethod;

/**
 * Represents a emoji of a channel
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage emoji
 */
class SmashcastEmoji implements IDownloadable {

    /**
     * Holds the relative url for this emoji
     * @var string
     */
    public $url;

    /**
     * Holds the abbreviation of this emoji
     * @var string
     */
    public $short;

    /**
     * Holds the alternative abbreviation of this emoji
     * @var string
     */
    public $shortAlt;

    /**
     * Cached stream of the downloaded emoji
     * @var string
     */
    private $stream;

    /**
     * @inheritDoc
     */
    public function download(string $location): bool {
        $stream = $this->getStream();

        // couldn't download it
        if($stream === null) {
            return false;
        }

        if(file_put_contents($location, $stream) === false) {
            // couldn't write it
            return false;
        }

        // yada yada yada!
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getStream(): ?string {
        if(null !== $this->stream) {
            return $this->stream;
        }

        try {
            $stream = SmashcastApi::getClient()
                ->request(HttpMethod::GET, $this->getPath())
                ->getBody()
                ->getContents();
        } catch(\Throwable $e) {
            // rethrow exception
            throw new SmashcastApiException('Cannot download the logo!', 0, $e);
            return null;
        }

        $this->stream = $stream;

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string {
        return SmashcastApi::IMAGE_URL . $this->url;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->getPath();
    }

}