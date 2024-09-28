<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Traits;

use IFlytek\Xfyun\Core\Traits\DecideRetryTrait;
use PHPUnit\Framework\TestCase;

class DecideRetryTraitTest extends TestCase
{
    use DecideRetryTrait;

    public function testGetNeverDecideRetryFunction()
    {
        $fun = $this->getDecideRetryFunction(false);
        $this->assertFalse($fun(new \Exception()));
    }
}
