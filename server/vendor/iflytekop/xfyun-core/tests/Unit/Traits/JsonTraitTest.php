<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Traits;

use IFlytek\Xfyun\Core\Traits\JsonTrait;
use PHPUnit\Framework\TestCase;

class JsonTraitTest extends TestCase
{
    use JsonTrait;

    public function testJsonEncode()
    {
        $result = "{\"test\":\"IFlytek\"}";
        $this->assertEquals(
            $result,
            $this->jsonEncode(
                ['test' => 'IFlytek']
            )
        );
    }

    public function testJsonDecode()
    {
        $data = "{\"test\":\"IFlytek\"}";
        $this->assertArrayHasKey('test', $this->jsonDecode($data, true));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonEncodeException()
    {
        try {
            $ch = curl_init();
            $this->jsonEncode($ch);
        } catch (\Error $e) {
            curl_close($ch);
        }
    }
}
