<?php
namespace jens1o\smashcast\emoji;

use jens1o\smashcast\util\RequestUtil;
use jens1o\util\HttpMethod;

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
class SmashcastChannelEmojis {

    /**
     * Holds the name of this channel
     * @var string
     */
    private $channelName;

    /**
     * Holds the list of (saved) emojis
     * @var SmashcastEmoji[][]
     */
    private $emojiList = [
        'premium' => [],
        'standard' => []
    ];

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
     * Returns the emojis for this channel, returns null on failure
     *
     * @param   bool    $premiumOnly    whether we only need sub-emojis
     * @param   bool    $skipCache      whether to skip the cache and get fresh data
     * @return Emoji[]|null
     */
    public function getEmojis(bool $premiumOnly = false, bool $skipCache = false): ?array {
        $arrayKey = $premiumOnly ? 'premium' : 'standard';

        if(!$skipCache && null !== $this->emojiList[$arrayKey] && $this->listIsComplete[$arrayKey]) {
            return $this->emojiList[$arrayKey];
        }

        try {
            $list = RequestUtil::doRequest(HttpMethod::GET, "chat/icons/{$this->channelName}", [
                'query' => [
                    'premiumOnly' => $premiumOnly ? 'true' : 'false'
                ]
            ]);
        } catch(SmashcastApiException $e) {
            // clear cache
            $this->emojiList[$arrayKey] = [];
            return null;
        }

        // refresh list
        $this->emojiList[$arrayKey] = array_map(function($emojiData) {
            $emoji = new SmashcastEmoji;

            $emoji->url = $emojiData->icon_path;
            $emoji->short = $emojiData->icon_short;
            $emoji->shortAlt = $emojiData->icon_short_alt;

            return $emoji;
        }, $list->items);

        return $this->emojiList[$arrayKey];
    }

}