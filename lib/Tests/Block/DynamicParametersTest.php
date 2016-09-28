<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\DynamicParameters;
use PHPUnit\Framework\TestCase;

class DynamicParametersTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\DynamicParameters
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new DynamicParameters(
            array(
                'param' => 'value',
                'closure_param' => function () {
                    return 'closure_value';
                },
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::__construct
     * @covers \Netgen\BlockManager\Block\DynamicParameters::getParameter
     */
    public function testGetParameter()
    {
        self::assertEquals('value', $this->collection->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::getParameter
     */
    public function testGetParameterWithClosureParam()
    {
        self::assertEquals('closure_value', $this->collection->getParameter('closure_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::getParameter
     */
    public function testGetParameterWithNonExistingParam()
    {
        self::assertEquals(null, $this->collection->getParameter('non_existing'));
    }
}
