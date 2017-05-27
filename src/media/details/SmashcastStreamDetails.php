<?php
namespace jens1o\smashcast\media\details;

use jens1o\smashcast\model\AbstractModel;
use jens1o\util\HttpMethod;

/**
 * Represents live stream details
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    namespace
 * @subpackage subpackage
 */
class SmashcastStreamDetails extends AbstractModel {

    /**
     * Holds the last log date.
     * @var \DateTime
     */
    private $logDate = null;

    /**
     * Creates a new SmashcastStreamDetails object.
     * **Warning!** This executes immediately a request to Smashcast fetching all data when `$row` is not provided!
     *
     * @param   string|null     $identifier     The name of the user, can be `null` when `$row` is provided
     * @param   mixed[]|null    $row            All information about the user fetched from the api, can be `null` when `$userName` is provided
     * @throws \BadMethodCallException  when both `$identifier` and `$row` are null
     * @throws SmashcastApiException  When getting data from the api failed
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null) {
        if($row !== null) {
            // prefer $row when provided, so we don't need to call their api again
            $this->data = $row;
        } elseif($identifier !== null) {
            // call their api (and pass the exception to the user :P)
            $response = $this->doRequest(HttpMethod::GET, 'mediainfo/live/' . $identifier, ['noAuthToken' => true]);
            if(isset($response->mediainfo)) {
                $this->data = $response->mediainfo;
            }
        } else {
            throw new \BadMethodCallException('Try to call ' . static::class . ' with both arguments null. One must be given!');
        }
    }

    /**
     * Returns the height of the stream
     *
     * @return int
     */
    public function getHeight(): int {
        return $this->data->height;
    }

    /**
     * Returns the width of the stream
     *
     * @return int
     */
    public function getWidth(): int {
        return $this->data->width;
    }

    /**
     * Returns the log id of the stream
     *
     * @return int
     */
    public function getLogId(): int {
        return $this->data->log_id;
    }

    /**
     * Returns the video bitrate of the stream
     *
     * @return int
     */
    public function getVideoBitrate(): int {
        return $this->data->vbitrate;
    }

    /**
     * Returns the audio bitrate of the stream
     *
     * @return int
     */
    public function getAudioBitrate(): int {
        return $this->data->abitrate;
    }

    /**
     * Returns the media profile. NOTE: level 3.1 = 31, level 4.0 = 40, level 4.1 = 41!
     *
     * @return string
     */
    public function getProfile(): string {
        return $this->data->profile;
    }

    /**
     * Returns the codec of the video
     *
     * @return string
     */
    public function getVideoCodec(): string {
        return $this->data->vcodec;
    }

    /**
     * Returns the codec of the audio
     *
     * @return string
     */
    public function getAudioCodec(): string {
        return $this->data->acodec;
    }

    /**
     * Returns how many frames per second will be shown in the video.
     *
     * @return int
     */
    public function getFps(): int {
        return $this->data->fps;
    }

    /**
     * Returns the gop.
     *
     * @return string|null
     */
    public function getGop(): ?string {
        return $this->data->gop;
    }

    /**
     * Returns the keyframe interval
     *
     * @return int
     */
    public function getKeyframeInterval(): int {
        return $this->data->kf_interval;
    }

    /**
     * Returns the user agent(currently null?)
     *
     * @return string|null
     */
    public function getUserAgent(): ?string {
        return $this->data->useragent;
    }
    
    /**
     * Returns the hostname of this stream.
     *
     * @return string
     */
    public function getHostName(): string {
        return $this->data->hostname;
    }

    /**
     * Returns the log date.
     *
     * @return \DateTime|null
     */
    public function getLogDate(): ?\DateTime {
        if($this->logDate === null) {
            if($this->data->log_date === null) {
                // shortcut
                return null;
            }

            try {
                $this->logDate = new \DateTime($this->data->log_date);
            } catch(\Throwable $e) {
                // failure, eat it.
                return null;
            }
        }

        return $this->logDate;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool {
        return $this->data->media_id !== null && $this->data->media_id !== '0';
    }

    /**
     * You MUST NOT update stream details!
     *
     * @deprecated
     * @ignore
     * @return void
     * @throws \BadMethodCallException in any case.
     */
    public function update(array $updateParts) {
        throw new \BadMethodCallException('You MUST NOT call this method!');
    }

    /**
    * You MUST NOT validate the properties to update stream details!
    *
    * @deprecated
    * @ignore
    * @return void
    * @throws \BadMethodCallException in any case.
    */
    public function validateUpdate(array $updateParts): bool {
        throw new \BadMethodCallException('You MUST NOT call this method!');
        return false;
    }

}