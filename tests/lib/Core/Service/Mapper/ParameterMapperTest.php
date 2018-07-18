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

    public function setUp(): void
    {
        $this->mapper = new ParameterMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper::mapParameters
     */
    public function testMapParameters(): void
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
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
        $this->assertSame($blockDefinition->getParameterDefinition('css_class'), $mappedParameters['css_class']->getParameterDefinition());
        $this->assertSame('some-class', $mappedParameters['css_class']->getValue());
        $this->assertFalse($mappedParameters['css_class']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['css_id']);
        $this->assertSame($blockDefinition->getParameterDefinition('css_id'), $mappedParameters['css_id']->getParameterDefinition());
        $this->assertSame('some-id', $mappedParameters['css_id']->getValue());
        $this->assertFalse($mappedParameters['css_id']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['compound']);
        $this->assertSame($blockDefinition->getParameterDefinition('compound'), $mappedParameters['compound']->getParameterDefinition());
        $this->assertTrue($mappedParameters['compound']->getValue());
        $this->assertFalse($mappedParameters['compound']->isEmpty());

        $this->assertInstanceOf(Parameter::class, $mappedParameters['inner']);
        $this->assertSame($compoundParameter->getParameterDefinition('inner'), $mappedParameters['inner']->getParameterDefinition());
        $this->assertSame('inner-value', $mappedParameters['inner']->getValue());
        $this->assertFalse($mappedParameters['inner']->isEmpty());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper::serializeValues
     */
    public function testSerializeValues(): void
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
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

        $this->assertSame(
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
    public function testExtractUntranslatableParameters(): void
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
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

        $this->assertSame(
            [
                'other' => null,
                'css_id' => 'some-id',
                'compound' => true,
                'inner' => 'inner-value',
            ],
            $untranslatableParams
        );
    }
}
