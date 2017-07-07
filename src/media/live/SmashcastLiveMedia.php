<?php
namespace jens1o\smashcast\media\live;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\exception\SmashcastApiException;
use jens1o\smashcast\hashtag\SmashcastHashtag;
use jens1o\smashcast\media\live\details\SmashcastStreamDetails;
use jens1o\smashcast\model\AbstractModel;
use jens1o\smashcast\util\RequestUtil;
use jens1o\util\HttpMethod;

/**
 * Represents live media from an user
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast\media
 * @subpackage live
 */
class SmashcastLiveMedia extends AbstractModel {

    /**
     * The default fields that will be used when the api provided some kind of invalid response.
     * Added to maintain some consistency that the api itself does not provide :(
     * @var string[]
     * @see https://www.youtube.com/watch?v=HP362ccZBmY
     */
    public static $defaultFields = [
        'media_user_name' => null,
        'media_id' => null,
        'media_file' => null,
        'media_user_id' => null,
        'media_profiles' => null,
        'media_type_id' => null,
        'media_is_live' => null,
        'media_live_delay' => null,
        'media_transcoding' => null,
        'media_chat_enabled' => null,
        'media_countries' => null,
        'media_offline_id' => null,
        'media_hosted_id' => null,
        'media_mature' => null,
        'media_hidden' => null,
        'user_banned' => null,
        'media_name' => null,
        'media_display_name' => null,
        'media_status' => null,
        'media_title' => null,
        'media_description' => null,
        'media_description_md' => null,
        'media_tags' => null,
        'media_duration' => null,
        'media_bg_image' => null,
        'media_views' => null,
        'media_views_daily' => null,
        'media_views_weekly' => null,
        'media_views_monthly' => null,
        'category_id' => null,
        'category_name' => null,
        'category_name_short' => null,
        'category_seo_short' => null,
        'category_viewers' => null,
        'category_media_count' => null,
        'category_channels' => null,
        'category_logo_small' => null,
        'category_logo_large' => null,
        'category_updated' => null,
        'team_name' => null,
        'media_start_in_sec' => null,
        'media_thumbnail' => null,
        'media_thumbnail_large' => null,
        'channel' => [
            'followers' => null,
            'videos' => null,
            'recordings' => null,
            'teams' => null,
            'user_id' => null,
            'user_name' => null,
            'user_logo' => null,
            'user_cover' => null,
            'user_logo_small' => null,
            'user_partner' => null,
            'partner_type' => null,
            'user_beta_profile' => null,
            'media_is_live' => null,
            'media_live_since' => null,
            'user_media_id' => null,
            'twitter_account' => null,
            'twitter_enabled' => null,
            'livestream_count' => null,
            'channel_link' => null
        ]
    ];

    /**
     * Holds the time when the channel had been created
     * @var \DateTime
     */
    private $dateCreated = null;

    /**
     * Creates a new Live Media object.
     * **Warning!** This executes immediately a request to Smashcast fetching all data when `$row` is not provided!
     *
     * @param   string|null     $identifier     The name of the user, can be `null` when `$row` is provided
     * @param   mixed[]|null    $row            All information about the user fetched from the api, can be `null` when `$userName` is provided
     * @throws \BadMethodCallException when `$identifier` and `$row` are null
     * @throws SmashcastApiException When getting data from the api failed
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null) {
        if(null !== $row) {
            // prefer $row when provided, so we don't need to call their api again
            $this->data = (object) array_merge(static::$defaultFields, (array) $row);
        } elseif(null !== $identifier) {
            // call the api
            $response = $this->doRequest(HttpMethod::GET, 'media/live/' . $identifier, ['appendAuthToken' => false]);
            if(isset($response->livestream)) {
                $this->data = (object) array_merge(static::$defaultFields, (array) $response->livestream[0]);
            } else {
                $this->data = (object) static::$defaultFields;
            }
        } else {
            throw new \BadMethodCallException('Try to call ' . static::class . ' with both arguments null. One must be given!');
        }
    }

    /**
     * Returns the stream details for this stream.
     *
     * @return SmashcastStreamDetails
     */
    public function getStreamDetails(): SmashcastStreamDetails {
        if($this->streamDetails === null) {
            $this->streamDetails = new SmashcastStreamDetails($this->data->media_id);
        }

        return $this->streamDetails;
    }

    /**
     * Returns a \DateTime containing the date when the channel was created. Returns `null`(not a string) on failure
     *
     * @return \DateTime|null
     */
    public function getTimeCreated(): ?\DateTime {
        if($this->dateCreated === null) {
            if($this->data->media_date_added === null) {
                // shortcut
                return null;
            }

            try {
                $this->dateCreated = new \DateTime($this->data->media_date_added);
            } catch(\Throwable $e) {
                // failure, eat it.
                return null;
            }
        }

        return $this->dateCreated;
    }

    /**
     * Returns whether the channel is live at the moment.
     *
     * @return bool
     */
    public function isLive(): bool {
        return null !== $this->data->media_is_live && $this->data->media_is_live !== '0';
    }

    /**
     * Returns the stream title, or null when an error occurred
     *
     * @return string|null
     */
    public function getStreamTitle(): ?string {
        return $this->data->media_status;
    }

    /**
     * Returns an list of instantiated hashtags
     *
     * @return SmashcastHashtag[]
     */
    public function getHashtags(): array {
        if($this->hashtags === null) {
            if($this->data->media_status === null) {
                // shortcut
                return [];
            }

            // TODO: Maybe put this inside another class to follow the single responsibility principle?

            preg_match_all('/#(\w+)/', $this->data->media_status, $matches);

            $this->hashtags = [];
            $tmp = [];
            foreach($matches[1] as $hashtag) {
                // fix for having one hashtag more than once
                if(!isset($tmp[$hashtag])) {
                    $this->hashtags[] = new SmashcastHashtag($hashtag);
                    $tmp[$hashtag] = true;
                }
            }
            unset($tmp);
        }

        return $this->hashtags;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool {
        return null !== $this->data->media_id;
    }

    /**
     * Updates the live media, for example the title. Returns `null`(not a string) on error, the updated instance on success.
     *
     * @param   mixed[]     $updateParts    The parts to update.
     * @return SmashcastUser|null
     * @throws SmashcastApiException When validating failed
     */
    public function update(array $updateParts): ?SmashcastLiveMedia {
        if(!$this->validateUpdate($updateParts)) return null;

        $tmp = (array) $this->data;

        // unset some data, because we want to access that api obeying the rules. But of course, you can overwrite it to make some action!
        unset($tmp['media_description']);
        unset($tmp['media_hosted_name']);

        $userSettings = array_merge($tmp, $updateParts);

        try {
            // discard response, it's just a copy of what we've sent before
            $this->doRequest(HttpMethod::PUT, 'media/live/' . $this->data->media_id, [
                'json' => [
                    'media_type' => 'live',
                    'authToken' => SmashcastApi::getUserAuthToken()->getToken(),
                    'livestream' => [$userSettings],
                    'media_name' => $this->data->media_id
                ],
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            throw new SmashcastApiException('Updating a live media failed!', 0, $e);
            return null;
        }

        // update the class itself
        $this->data = (object) array_merge((array) $this->data, $updateParts);

        return $this;
    }

    /**
     * Validates and returns whether this update process is valid.
     *
     * @param   mixed[]     $updateParts    The parts to validate.
     * @return bool
     * @throws SmashcastApiException When validating failed
     */
    public function validateUpdate(array $updateParts): bool {
        if(!$this->exists()) {
            throw new SmashcastApiException('Non existing live media cannot be updated.');
            return false;
        }

        if(!$this->isAuthenticated()) {
            throw new SmashcastApiException('You need to be authenticated to update medias.');
            return false;
        }

        // check for forbidden fields
        $forbiddenFields = ['media_user_name', 'media_id', 'channel'];
        $failedFields = [];
        foreach($forbiddenFields as $forbiddenField) {
            if(array_key_exists($forbiddenField, $updateParts)) {
                $failedFields[] = $forbiddenField;
            }
        }

        if([] !== $failedFields) {
            throw new SmashcastApiException('You MUST omit ' . implode(', ', $failedFields) . ' when trying to update an user object!');
            return false;
        }

        return true;
    }

}