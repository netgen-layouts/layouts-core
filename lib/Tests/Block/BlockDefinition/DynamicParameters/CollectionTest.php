<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\DynamicParameters;

use Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new Collection(
            array(
                'param' => 'value',
                'closure_param' => function () {
                    return 'closure_value';
                },
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection::getParameter
     */
    public function testGetParameter()
    {
        self::assertEquals('value', $this->collection->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection::getParameter
     */
    public function testGetParameterWithClosureParam()
    {
        self::assertEquals('closure_value', $this->collection->getParameter('closure_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicParameters\Collection::getParameter
     */
    public function testGetParameterWithNonExistingParam()
    {
        self::assertEquals(null, $this->collection->getParameter('non_existing'));
    }
}
