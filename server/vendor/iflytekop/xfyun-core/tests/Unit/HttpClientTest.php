<?php

namespace IFlytek\Xfyun\Core\Tests\Unit;

use IFlytek\Xfyun\Core\HttpClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class HttpClientTest extends TestCase
{

    private $client;

    private static $requestOptions = [
        'timeout' => 2
    ];

    public function setUp()
    {
        $this->client = new HttpClient([]);
    }

    public function testHttpRequestSuccessfullySendAndReceive()
    {
        $result = $this->client->sendAndReceive(
            new Request('GET', 'http://www.example.com'),
            self::$requestOptions
        );
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @expectedException GuzzleHttp\Exception\ServerException
     */
    public function testHttpRequestTryThreeTime()
    {
        $this->client->sendAndReceive(
            new Request('GET', 'http://test.hotchicken.cn/Return502.php'),
            self::$requestOptions
        );
    }

    /**
     * @expectedException GuzzleHttp\Exception\ConnectException
     */
    public function testHttpRequestConnectError()
    {
        $this->client->sendAndReceive(
            new Request('GET', 'http://test.hotchicken.cn/RandomDelay3to4Sec.php'),
            self::$requestOptions
        );
    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testReturnRetryMessageSoRetry()
    {
        $this->client->sendAndReceive(
            new Request('GET', 'http://test.hotchicken.cn/ReturnRetryMessage.php'),
            self::$requestOptions
        );
    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testReturnInvalidRetryMessageSoRetry()
    {
        $this->assertEquals(
            200,
            $this->client->sendAndReceive(
                new Request('GET', 'http://test.hotchicken.cn/ReturnInvalidRetryMessage.php'),
                self::$requestOptions
            )
        );
    }
}
