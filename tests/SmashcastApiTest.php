<?php
namespace jens1o\smashcast\test;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use jens1o\smashcast\SmashcastApi;
use PHPUnit\Framework\TestCase;

class SmashcastApiTest extends TestCase {

    public function testGetClientReturnsAClient() {
        $client = SmashcastApi::getClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testAllUrlsAreSecured() {
        foreach([SmashcastApi::BASE_URL, SmashcastApi::IMAGE_URL] as $url) {
            $this->assertStringStartsWith('https://', $url, 'The url ' . $url . ' is not secured!');
        }
    }
    
    /**
     * @depends testGetClientReturnsAClient
     */
    public function testBaseUrlReturns500() {
        // 500 is the response of the api host when accessing the normal host...
        $client = SmashcastApi::getClient();
        $statusCode = 200;

        try {
            $client->get('');
        } catch(GuzzleException $e) {
            if($e->getResponse() === null) {
                $this->markTestSkipped('Couldn\'t connect to the api!');
            }

            $statusCode = $e->getResponse()->getStatusCode();
        }

        $this->assertEquals(500, $statusCode);
    }

    /**
     * @depends testGetClientReturnsAClient
     */
    public function testImageUrlReturns200() {
        // 200 is the response of the image host when accessing the normal host...
        $client = SmashcastApi::getClient();
        $statusCode = 200;

        try {
            $client->get(SmashcastApi::IMAGE_URL);
        } catch(GuzzleException $e) {
            if($e->getResponse() === null) {
                $this->markTestSkipped('Couldn\'t connect to the api!');
            }

            $statusCode = $e->getResponse()->getStatusCode();
        }

        $this->assertEquals(200, $statusCode);
    }

}