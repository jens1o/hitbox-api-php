<?php
namespace jens1o\smashcast\channel;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\SmashcastApiException;
use jens1o\smashcast\model\AbstractModel;
use jens1o\smashcast\util\{HttpMethod, RequestUtil};

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
        $this->channelName = strtolower($identifier);
    }

    /**
     * Returns the channel name
     *
     * @return string
     */
    public function getChannelName(): string {
        return $this->channelName;
    }

    /**
     * Returns the stream key for this channel, or null when an error occurred.
     *
     * @return string|null
     * @todo Test that method when I do not stream c: But seems to work c:
     */
    public function getStreamKey(): ?string {
        try {
            $response = RequestUtil::doRequest(HttpMethod::GET, 'mediakey/' . $this->channelName, [
                'appendAuthToken' => false,
            ], true);
        } catch(SmashcastApiException $e) {
            return null;
        }

        if(isset($response->streamKey)) {
            return $response->streamKey;
        }

        return null;
    }

    /**
     * Resets the stream key for the channel. Do not test this while streaming. Returns null on failure, and new key as a string when successful
     *
     * @return string|null
     * @todo Test this function as soon as I go offline <3
     */
    public function resetStreamKey(): ?string {
        try {
            $response = RequestUtil::doRequest(HttpMethod::PUT, 'mediakey/' . $this->channelName, [
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            return null;
        }

        if(isset($response->streamKey)) {
            return $response->streamKey;
        }

        return null;
    }

    /**
     * Returns the list of editors for this channel or null when an error occurred.
     *
     * @return mixed[]|null
     */
    public function getEditors(): ?array {
        try {
            $response = RequestUtil::doRequest(HttpMethod::GET, 'editors/' . $this->channelName, [
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            return null;
        }

        if(isset($response->list)) {
            return $response->list;
        }

        return null;
    }

    /**
     * Adds `$username` as an editor to this channel. Returns wether the action has been completed successfully.
     *
     * @param   string  $userName The name of the user you want to add as an editor.
     * @return bool
     */
    public function addEditor(string $userName): bool {
        if($this->channelName === strtolower($userName)) {
            throw new \BadMethodCallException('You may not want to add yourself as an editor!');
            return false;
        }

        try {
            $request = RequestUtil::doRequest(HttpMethod::POST, 'editor/' . $this->channelName, [
                'json' => [
                    'authToken' => SmashcastApi::getUserAuthToken()->getToken(),
                    'editor'=> $userName,
                    'remove' => false
                ]
            ]);
        } catch(SmashcastApiException $e) {
            return false;
        }

        if(isset($response->message) && $response->message === 'success') {
            return true;
        }

        return false;
    }

    // TODO: Get a person which tests running ads. Or can I do that myself?

}