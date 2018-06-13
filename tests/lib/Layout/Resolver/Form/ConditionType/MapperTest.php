<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     */
    public function testGetFormOptions()
    {
        $this->assertEquals(
            [],
            $this->mapper->getFormOptions()
        );
    }
}
