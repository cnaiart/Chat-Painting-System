<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Handler;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function onlyGuzzle6()
    {
        if ($this->getGuzzleMajorVersion() !== 6) {
            $this->markTestSkipped('Guzzle 6 only');
        }
    }

    protected function onlyGuzzle7()
    {
        if ($this->getGuzzleMajorVersion() !== 7) {
            $this->markTestSkipped('Guzzle 7 only');
        }
    }

    protected function getGuzzleMajorVersion()
    {
        if (defined('GuzzleHttp\ClientInterface::MAJOR_VERSION')) {
            return ClientInterface::MAJOR_VERSION;
        }

        if (defined('GuzzleHttp\ClientInterface::VERSION')) {
            return (int) substr(ClientInterface::VERSION, 0, 1);
        }

        $this->fail('Unable to determine the currently used Guzzle Version');
    }
}
