<?php
namespace jens1o\smashcast\emoji;

use jens1o\smashcast\model\AbstractModel;

/**
 * Manages the emojis of a channel
 *
 * **WARNING:** This api does not care nor throws an exception when the channel does not exist.
 * It justs returns all default emojis... Please check first whether the channel exists...
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage emojis
 */
class SmashcastChannelEmojis extends AbstractModel {

    /**
     * Holds the name of this channel
     * @var string
     */
    private $channelName;

    /**
     * Holds the list of (saved) emojis
     * @var SmashcastEmoji[][]
     */
    private $emojiList;

    /**
     * Holds whether the `$emojiList` is complete
     * @var bool[][]
     */
    private $listIsComplete = [
        'premium' => false,
        'standard' => false
    ];

    /**
     * Creates a new channel object based on the name.
     *
     * @param   string  $identifier     The identifier for the name
     */
    public function __construct(string $identifier) {
        // The apis called in this class don't care about whether the channelname is uppercased or not...
        $this->channelName = $identifier;
    }

    /**
     * Returns the emojis for this channel
     *
     * @param   bool    $premiumOnly    whether we only need sub-emojis
     * @param   bool    $skipCache      whether to skip the cache and get fresh data
     * @return \stdClass[]
     */
    public function getEmojis(bool $premiumOnly = false, bool $skipCache = false): array {
        $arrayKey = $premiumOnly ? 'premium' : 'standard';

        if(!$skipCache && null !== $this->emojiList[$arrayKey] && $this->listIsComplete[$arrayKey]) {
            return $this->emojiList[$arrayKey];
        }


    }

}