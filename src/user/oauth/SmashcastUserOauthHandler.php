<?php
namespace jens1o\smashcast\user\oauth;

use jens1o\smashcast\exception\SmashcastApiException;
use jens1o\smashcast\model\AbstractModel;
use jens1o\smashcast\util\{HttpMethod, RequestUtil};

/**
 * Holds a list of oauth applications an user has authenticated with.
 *
 * @author     jens1o
 * @copyright  Jens Hausdorf 2017
 * @license    MIT License
 * @package    jens1o\smashcast\user
 * @subpackage oauth
 */
class SmashcastUserOauthList implements \Iterator {

    /**
     * Internal position for the iterator
     * @var int
     */
    private $position = 0;

    /**
     * Holds the owner of this list
     * @var string
     */
    private $userName;

    /**
     * Holds a list with instantiated \stdClasses representing all applications
     * @var \stdClass[]
     */
    private $list = null;

    /**
     * Creates a new list by an username.
     * **Warning!** This immediately executes a request to the api asking for the applications!
     *
     * @param   string  $userName   On what user do we want the list?
     */
    public function __construct(string $userName) {
        $this->username = $userName;
        $this->loadContent();
    }

    /**
     * Loads the list
     *
     * @throws SmashcastApiException
     */
    public function loadContent() {
        try {
            $response = RequestUtil::doRequest(HttpMethod::GET, 'oauthaccess/' . $this->username, [
                'appendAuthToken' => false
            ], true);
        } catch(SmashcastApiException $e) {
            throw new SmashcastApiException('Fetching the list for the username from the api failed!', 0, $e);
        }

        $this->list = $response->apps;
    }

    /**
     * Returns the instantiated list representing all oauth applications
     *
     * @return \stdClass[]
     */
    public function getList(): array {
        return $this->list;
    }

    /**
     * @inheritDoc
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    public function current() {
        return $this->list[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function key() {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function next() {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid() {
        return isset($this->list[$this->position]);
    }

}