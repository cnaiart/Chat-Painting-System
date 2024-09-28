<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Handler;

use IFlytek\Xfyun\Core\Handler\Guzzle7HttpHandler;

/**
 * @group http-handler
 */
class Guzzle7HttpHandlerTest extends Guzzle6HttpHandlerTest
{
    public function setUp()
    {
        $this->onlyGuzzle7();

        $this->client = $this->prophesize('GuzzleHttp\ClientInterface');
        $this->handler = new Guzzle7HttpHandler($this->client->reveal());
    }
}
