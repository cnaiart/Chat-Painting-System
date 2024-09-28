<?php

namespace IFlytek\Xfyun\Core\Tests\Unit;

use IFlytek\Xfyun\Core\WsClient;
use IFlytek\Xfyun\Core\Handler\WsHandler;
use IFlytek\Xfyun\Core\Traits\SignTrait;
use IFlytek\Xfyun\Core\Traits\JsonTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class WsClientTest extends TestCase
{
    use SignTrait;
    use JsonTrait;

    public function testWsClientRequestSuccessfullySendAndReceive()
    {
        $credentials = array_merge(
            Yaml::parseFile(__DIR__ . '/./credentials.yml'),
            [
                'appId' => getenv('PHPSDK_CORE_APPID'),
                'apiKey' => getenv('PHPSDK_CORE_APIKEY'),
                'apiSecret' => getenv('PHPSDK_CORE_APISECRET')
            ]
        );


        $logger = new Logger('my_logger');
        $logger->pushHandler(new StreamHandler(__DIR__.'/unit.test.log', Logger::DEBUG));
        $client = new WsClient(
            [
                'handler' => new WsHandler(
                    $this->signUriV1('wss://tts-api.xfyun.cn/v2/tts', $credentials),
                    $credentials['input'],
                    300,
                    $logger
                )
            ]
        );
        $result = $client->sendAndReceive();
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('58173aacf1dabcb66fcd3fb7c54c0d68', md5($result->getBody()));
    }

    /**
     * @expectedException WebSocket\ConnectionException
     */
    public function testWsClientRequestException()
    {
        $client = new WsClient(
            [
                'handler' => new WsHandler(
                    'wss://tts-api.xfyun.cn/v2/tts',
                    'input'
                )
            ]
        );
        $result = $client->sendAndReceive();
    }

    public function testWsClientSendAndReceive()
    {
        $credentials = array_merge(
            Yaml::parseFile(__DIR__ . '/./credentials.yml'),
            [
                'appId' => getenv('PHPSDK_CORE_APPID'),
                'apiKey' => getenv('PHPSDK_CORE_APIKEY'),
                'apiSecret' => getenv('PHPSDK_CORE_APISECRET')
            ]
        );

        $client = new WsClient(
            [
                'handler' => new WsHandler(
                    $this->signUriV1('wss://tts-api.xfyun.cn/v2/tts', $credentials),
                    $credentials['input']
                )
            ]
        );
        $this->assertNull($client->send());
        $result = $this->jsonDecode($client->receive());
        $this->assertEquals($result->code, 0);
    }
}
