<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Traits;

use GuzzleHttp\Psr7\Request;
use IFlytek\Xfyun\Core\HttpClient;
use IFlytek\Xfyun\Core\Traits\SignTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class SignTraitTest extends TestCase
{
    use SignTrait;

    public function testSuccessfullySignUri()
    {
        $result = 'wss://tts-api.xfyun.cn/v2/tts?host=tts-api.xfyun.cn&date=Mon%2C+05+Apr+2021+07%3A09%3A53+GMT&authorization=YXBpX2tleT0iOWEzMzk0ODc2M2FjZmNkZTUzMTEwZDUyODNlOTliMDIiLGFsZ29yaXRobT0iaG1hYy1zaGEyNTYiLGhlYWRlcnM9Imhvc3QgZGF0ZSByZXF1ZXN0LWxpbmUiLHNpZ25hdHVyZT0iWnZ2STI0ZStEallQbTZHajRhejE5QTJiTGU3Q0xUSngwdnFZQmwwam9lcz0i';
        $this->assertEquals(
            $result,
            $this->signUriV1(
                'wss://tts-api.xfyun.cn/v2/tts',
                array_merge(
                    Yaml::parseFile(__DIR__ . '/../credentials.yml'),
                    [
                        'appId' => getenv('PHPSDK_CORE_APPID'),
                        'apiKey' => getenv('PHPSDK_CORE_APIKEY'),
                        'apiSecret' => getenv('PHPSDK_CORE_APISECRET')
                    ]
                )
            )
        );
    }

    public function testSuccessfullySignV1()
    {
        $this->assertEquals(
            'IrrzsJeOFk1NGfJHW6SkHUoN9CU=',
            $this->signV1('595f23df', 'd9f4aa7ea6d94faca62cd88a28fd5234', '1512041814')
        );
    }

    public function testSuccessfullySignV2()
    {
        $this->assertNotNull(getenv('PHPSDK_CORE_APPID'));
        $this->assertNotNull(getenv('PHPSDK_CORE_QBH_SECRETKEY'));
        $result = $this->signV2(
            getenv('PHPSDK_CORE_APPID'),
            getenv('PHPSDK_CORE_QBH_SECRETKEY'),
            '{"aue":"raw","sample_rate":"16000"}'
        );
        $this->assertEquals(getenv('PHPSDK_CORE_APPID'), $result['X-Appid']);
        $this->assertEquals('eyJhdWUiOiJyYXciLCJzYW1wbGVfcmF0ZSI6IjE2MDAwIn0=', $result['X-Param']);
        $client = new HttpClient([
            'httpHeaders' => $result
        ]);
        $result = $client->sendAndReceive(
            new Request('POST', 'https://webqbh.xfyun.cn/v1/service/v1/qbh')
        )->getBody()->getContents();
        // 返回数据非法则表示鉴权通过
        $this->assertTrue(strpos($result, 'invalid data') > 0);
    }
}
