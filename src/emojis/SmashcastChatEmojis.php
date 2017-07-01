<?php
namespace jens1o\smashcast\emojis;

use jens1o\smashcast\model\AbstractModel;

/**
 * Manages the emojis of a channel
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage emojis
 */
class SmashcastChatEmojis extends AbstractModel {

    /**
     * Holds the name of this channel
     * @var string
     */
    private $channelName;

    /**
     * Holds the list of (saved) emojis
     * @var Emoji[]
     */
    private $emojiList;

    /**
     * Holds whether the `$emojiList` is complete
     * @var bool
     */
    private $listIsComplete = false;

    /**
     * Creates a new channel object based on the name.
     *
     * @param   string  $identifier     The identifier for the name
     */
    public function __construct(string $identifier) {
        $this->channelName = strtolower($identifier);
    }

    /**
     * Returns the emojis for this channel
     *
     * @param   bool    $premiumOnly    whether we only need sub-emojis
     * @param   bool    $skipCache      whether to skip the cache and get fresh data
     * @return Emoji[]
     */
    public function getEmojis(bool $premiumOnly = false, bool $skipCache = false): array {
        if(!$skipCache && null !== $this->emojiList && $this->listIsComplete) {
            // TODO: Make logic happen!
        }
    }

}