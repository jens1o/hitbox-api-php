<?php
namespace jens1o\hitbox\model;

use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\util\RequestUtil;

abstract class AbstractModel implements IModel {

    /**
     * Saved data about the model
     * @var mixed[]
     */
    public $data = null;


    /**
     * Wether the optional auth token will be appended or set via a GET parameter
     */
    private $appendAuthToken = false;

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


}