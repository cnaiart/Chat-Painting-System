<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Handler;

use IFlytek\Xfyun\Core\Handler\Guzzle6HttpHandler;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Guzzle6HttpHandlerTest extends BaseTest
{
    protected $client;
    protected $handler;

    public function setUp()
    {
        $this->onlyGuzzle6();

        $this->client = $this->prophesize('GuzzleHttp\ClientInterface');
        $this->handler = new Guzzle6HttpHandler($this->client->reveal());
    }

    public function testSuccessfullySendsRequest()
    {
        $request = new Request('GET', 'https://domain.tld');
        $options = ['key' => 'value'];
        $response = new Response(200);

        $this->client->send($request, $options)->willReturn($response);

        $handler = $this->handler;

        $this->assertSame($response, $handler($request, $options));
    }
}
