<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\Parameter\TextLine;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Validator\Constraints\NotBlank;
use PHPUnit\Framework\TestCase;

class ParameterHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = $this->getMockForAbstractClass(ParameterHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(array(), $this->parameterHandler->convertOptions(new TextLine()));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getDefaultOptions
     */
    public function testGetDefaultOptions()
    {
        self::assertEquals(
            array(
                'required' => true,
                'label' => 'label.name',
                'property_path' => 'parameters[name]',
                'constraints' => array(
                    new NotBlank(
                        array('groups' => array('group'))
                    ),
                ),
            ),
            $this->parameterHandler->getDefaultOptions(
                new TextLine(array(), true),
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
