<?php
namespace jens1o\smashcast\channel;

use jens1o\smashcast\model\AbstractModel;

/**
 * Represents a channel which can host other channels, is decorated with videos, has a chat...
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage channel
 */
class SmashcastChannel {

    /**
     * Holds the channel name
     * @var string
     */
    private $channelName;

    /**
     * Creates a new channel object based on the name.
     *
     * @param   string  $identifier     The identifier for the name
     */
    public function __construct(string $identifier) {
        $this->channelName = $identifier;
    }

}