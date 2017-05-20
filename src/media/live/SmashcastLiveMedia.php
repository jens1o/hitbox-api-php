<?php
namespace jens1o\smashcast\media\live;

use jens1o\smashcast\exception\SmashcastApiException;
use jens1o\smashcast\model\AbstractModel;

/**
 * Represents live media from an user
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    namespace
 * @subpackage subpackage
 */
class SmashcastLiveMedia extends AbstractModel {

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
            $this->data = $row;
        } elseif($identifier !== null) {
            // call their api
            $response = $this->doRequest(HttpMethod::GET, 'media/live/' . $identifier, ['appendAuthToken' => false]);
            if(isset($response->livestream)) {
                $this->data = $response->livestream[0];
            }
        } else {
            throw new \BadMethodCallException('Try to call ' . self::class . ' with both arguments null. One must be given!');
        }
    }

    public function update(array $updateParts) {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function validateUpdate(array $updateParts): bool {
        throw new \BadMethodCallException('Not implemented.');
    }

}