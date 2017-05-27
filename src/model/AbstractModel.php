<?php
namespace jens1o\smashcast\model;

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\util\RequestUtil;

/**
 * Abstract implementation for a model
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast
 * @subpackage model
 */
abstract class AbstractModel implements IModel, IUpdatable {

    /**
     * Saved data about the model
     * @var mixed[]
     */
    public $data = null;

    /**
     * Executes the request and returns a json-decoded array
     *
     * @param   string      $method             With which http method it should request
     * @param   mixed[]     $parameters         Parameters for the request
     * @param   bool        $needsAuthToken     Wether this request **requires** an auth token.
     * @throws \BadMethodCallException When `$needsAuthToken` is true and no auth token was set
     * @see RequestUtil::doRequest()
     */
    public function doRequest(string $method, string $path, array $parameters = [], bool $needsAuthToken = null) {
        return RequestUtil::doRequest($method, $path, $parameters, $needsAuthToken);
    }

    /**
     * @inheritDoc
     */
    public function __get($needle) {
        if(isset($this->data->$needle)) {
            return $this->data->$needle;
        }

        return null;
    }

    /**
     * Returns the data this model requested from the api
     *
     * @return \stdClass
     */
    public function getData(): \stdClass {
        return $this->data;
    }

    /**
     * Returns true when there is an auth token
     *
     * @return bool
     */
    public function isAuthenticated(): bool {
        return SmashcastApi::getUserAuthToken() !== null;
    }
}