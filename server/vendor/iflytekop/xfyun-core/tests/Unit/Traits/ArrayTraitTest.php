<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Traits;

use IFlytek\Xfyun\Core\Traits\ArrayTrait;
use PHPUnit\Framework\TestCase;

class ArrayTraitTest extends TestCase
{
    use ArrayTrait;

    public function testSuccessfullyRemoveNull()
    {
        $this->assertEquals(
            [],
            $this->removeNull(['test' => null])
        );
        $this->assertEquals(
            [['a' => 1]],
            $this->removeNull(
                ['test' => null, ['a' => 1, 'test' => null]]
            )
        );
    }
}
