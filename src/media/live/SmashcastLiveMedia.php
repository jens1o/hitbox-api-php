<?php
namespace jens1o\smashcast\media\live;

use jens1o\smashcast\exception\SmashcastApiException;
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
     *
     * @var string[]
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
     * Creates a new Live Media object.
     * **Warning!** This executes immediately a request to Smashcast fetching all data when `$row` is not provided!
     *
     * @param   string|null     $identifier     The name of the user, can be `null` when `$row` is provided
     * @param   mixed[]|null    $row            All information about the user fetched from the api, can be `null` when `$userName` is provided
     * @throws \BadMethodCallException  when `$identifier` and `$row` are null
     * @throws SmashcastApiException
     */
    public function __construct(?string $identifier = null, ?\stdClass $row = null) {
        if($row !== null) {
            // prefer $row when provided, so we don't need to call their api again
            $this->data = (object) array_merge($row, static::$defaultFields);
        } elseif($identifier !== null) {
            // call their api
            try {
                $response = $this->doRequest(HttpMethod::GET, 'media/live/' . $identifier, ['appendAuthToken' => false]);
                if(isset($response->livestream)) {
                    $this->data = (object) array_merge($response->livestream[0], static::$defaultFields);
                }
            } catch(SmashcastApiException $e) {
                $this->data = (object) static::$defaultFields;
            }
        } else {
            throw new \BadMethodCallException('Try to call ' . static::class . ' with both arguments null. One must be given!');
        }
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool {
        return $this->data->media_id !== null;
    }

    public function update(array $updateParts) {
        throw new \BadMethodCallException('Not implemented yet.');
    }

    public function validateUpdate(array $updateParts): bool {
        throw new \BadMethodCallException('Not implemented yet.');
    }

}