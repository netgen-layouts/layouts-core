<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean;
use Netgen\BlockManager\Parameters\FormMapper\CompoundParameterHandler;
use Netgen\BlockManager\Parameters\Parameter\TextLine;
use PHPUnit\Framework\TestCase;

class CompoundParameterHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = $this->getMockForAbstractClass(CompoundParameterHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\CompoundParameterHandler::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(
            array(
                'parameters' => array(
                    'param' => new TextLine(),
                ),
            ),
            $this->parameterHandler->convertOptions(
                new Boolean(array('param' => new TextLine()))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\CompoundParameterHandler::getDefaultOptions
     */
    public function testGetDefaultOptions()
    {
        self::assertEquals(
            array(
                'label' => false,
                'parameter_validation_groups' => array('group'),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            ),
            $this->parameterHandler->getDefaultOptions(
                new Boolean(),
                'name',
                array(
                    'parameter_validation_groups' => array('group'),
                    'label_prefix' => 'label',
                    'property_path_prefix' => 'parameters',
                )
            )
        );
    }
}
