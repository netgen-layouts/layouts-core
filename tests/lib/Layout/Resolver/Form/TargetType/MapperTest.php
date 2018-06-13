<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     */
    public function testGetFormOptions()
    {
        $this->assertEquals(
            [],
            $this->mapper->getFormOptions()
        );
    }
}
