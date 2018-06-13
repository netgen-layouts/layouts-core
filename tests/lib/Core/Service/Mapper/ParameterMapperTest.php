<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithCompoundParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use PHPUnit\Framework\TestCase;

final class ParameterMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new ParameterMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper::mapParameters
     */
    public function testMapParameters()
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = new BlockDefinition(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ]
        );

        $mappedParameters = $this->mapper->mapParameters(
            $blockDefinition,
            [
                'css_id' => 'some-id',
                'compound' => true,
                'inner' => 'inner-value',
            ]
        );

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundParameter */
        $compoundParameter = $blockDefinition->getParameterDefinition('compound');

        $this->assertCount(4, $mappedParameters);
        $this->assertArrayHasKey('css_class', $mappedParameters);
        $this->assertArrayHasKey('css_id', $mappedParameters);
        $this->assertArrayHasKey('compound', $mappedParameters);
        $this->assertArrayHasKey('inner', $mappedParameters);

        $this->assertInstanceOf(Parameter::class, $mappedParameters['css_class']);
        $this->assertEquals($blockDefinition->getParameterDefinition('css_class'), $mappedParameters['css_class']->getParameterDefinition());
        $this->assertEquals('some-class', $mappedParameters['css_class']->getValue());
        $this->assertFalse($mappedParameters['css_class']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['css_id']);
        $this->assertEquals($blockDefinition->getParameterDefinition('css_id'), $mappedParameters['css_id']->getParameterDefinition());
        $this->assertEquals('some-id', $mappedParameters['css_id']->getValue());
        $this->assertFalse($mappedParameters['css_id']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['compound']);
        $this->assertEquals($blockDefinition->getParameterDefinition('compound'), $mappedParameters['compound']->getParameterDefinition());
        $this->assertTrue($mappedParameters['compound']->getValue());
        $this->assertFalse($mappedParameters['compound']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['inner']);
        $this->assertEquals($compoundParameter->getParameterDefinition('inner'), $mappedParameters['inner']->getParameterDefinition());
        $this->assertEquals('inner-value', $mappedParameters['inner']->getValue());
        $this->assertFalse($mappedParameters['inner']->isEmpty());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper::serializeValues
     */
    public function testSerializeValues()
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = new BlockDefinition(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ]
        );

        $serializedParameters = $this->mapper->serializeValues(
            $blockDefinition,
            [
                'css_class' => 'some-class',
                'compound' => true,
                'inner' => 'inner-value',
            ]
        );

        $this->assertEquals(
            [
                'css_class' => 'some-class',
                'compound' => true,
                'inner' => 'inner-value',
            ],
            $serializedParameters
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper::extractUntranslatableParameters
     */
    public function testExtractUntranslatableParameters()
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $blockDefinition = new BlockDefinition(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ]
        );

        $untranslatableParams = $this->mapper->extractUntranslatableParameters(
            $blockDefinition,
            [
                'css_id' => 'some-id',
                'css_class' => 'some-class',
                'compound' => true,
                'inner' => 'inner-value',
            ]
        );

        $this->assertEquals(
            [
                'css_id' => 'some-id',
                'compound' => true,
                'inner' => 'inner-value',
                'other' => null,
            ],
            $untranslatableParams
        );
    }
}
