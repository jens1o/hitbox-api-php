<?php
namespace jens1o\smashcast\hashtag;

/**
 * Represents a hashtag
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage hashtag
 */
class SmashcastHashtag {

    /**
     * String of a hashtag excluding `#`
     * @var string
     */
    private $hashtag;

    /**
     * Base url for hashtags.
     */
    public const HASHTAG_URL = 'https://www.smashcast.tv/browse/hashtags/%s';

    /**
     * Creates a new hashtag.
     * Note: We do not check for the hashtag, because string comparing is very slow in php and maybe the streamer wants to put two hashtags?
     *
     * @param   string  $hashtag    The text of the hashtag(excluding `#`)
     */
    public function __construct(string $hashtag) {
        $this->hashtag = strtolower($hashtag);
    }

    /**
     * Returns the plain hashtag
     *
     * @return string
     */
    public function getHashtag(): string {
        return $this->hashtag;
    }

    /**
     * Returns the hashtag with a prepended `#`
     *
     * @return string
     */
    public function __toString(): string {
        return '#' . $this->hashtag;
    }

    /**
     * Returns the link of this hashtag
     *
     * @return string
     */
    public function getLink(): string {
        return sprintf(self::HASHTAG_URL, $this->hashtag);
    }

}