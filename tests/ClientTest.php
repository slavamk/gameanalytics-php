<?php namespace MaartenStaa\GameAnalytics;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use Http\Adapter\Guzzle6HttpAdapter;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \MaartenStaa\GameAnalytics\Client
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getSecret
     * @covers ::getHttp
     */
    public function testConstruction()
    {
        $http = new Guzzle6HttpAdapter(new GuzzleClient(array(
            'handler' => new MockHandler,
        )));
        $client = new Client('aaaaa', 'bbbbb', $http);

        $this->assertEquals('bbbbb', $client->getSecret());
        $this->assertSame($http, $client->getHttp());

        $client = new Client('ccccc', 'ddddd');
        $this->assertEquals('ddddd', $client->getSecret());
        $this->assertNotNull($client->getHttp());
    }

    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $http = new Guzzle6HttpAdapter(new GuzzleClient(array(
            'handler' => new MockHandler,
        )));
        $client = new Client('aaaaa', 'bbbbb', $http);

        $this->assertEquals(
            $client::API_ENDPOINT . $client::API_VERSION . '/aaaaa/foo',
            $client->getEndpoint('foo')
        );
    }

    /**
     * @covers ::sandbox
     * @covers ::getEndpoint
     */
    public function testSandbox()
    {
        $http = new Guzzle6HttpAdapter(new GuzzleClient(array(
            'handler' => new MockHandler,
        )));
        $client = new Client('aaaaa', 'bbbbb', $http);

        $client->sandbox(true);

        $this->assertEquals(
            $client::API_ENDPOINT_SANDBOX . $client::API_VERSION . '/aaaaa/bar',
            $client->getEndpoint('bar')
        );

        $client->sandbox(false);

        $this->assertEquals(
            $client::API_ENDPOINT . $client::API_VERSION . '/aaaaa/baz',
            $client->getEndpoint('baz')
        );
    }

    /**
     * @covers ::init
     */
    public function testInit()
    {
        $http = new Guzzle6HttpAdapter(new GuzzleClient(array(
            'handler' => new MockHandler,
        )));
        $client = new Client('aaaaa', 'bbbbb', $http);

        $init = $client->init();
        $this->assertInstanceOf('MaartenStaa\GameAnalytics\Message', $init);
        $this->assertSame($client, $init->getClient());
        $this->assertStringEndsWith('aaaaa/init', $init->getEndpoint());
    }

    /**
     * @covers ::event
     */
    public function testEvent()
    {
        $http = new Guzzle6HttpAdapter(new GuzzleClient(array(
            'handler' => new MockHandler,
        )));
        $client = new Client('aaaaa', 'bbbbb', $http);

        $event = $client->event('foo');
        $this->assertInstanceOf('MaartenStaa\GameAnalytics\Message', $event);
        $this->assertSame($client, $event->getClient());
        $this->assertStringEndsWith('aaaaa/events', $event->getEndpoint());
        $this->assertEquals(['category' => 'foo'], $event->getPayload());
    }
}