<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Core\Mapper\ParameterMapper;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandlerWithCompoundParameter;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterMapper::class)]
final class ParameterMapperTest extends TestCase
{
    private ParameterMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ParameterMapper();
    }

    public function testMapParameters(): void
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ],
        );

        $mappedParameters = [
            ...$this->mapper->mapParameters(
                $blockDefinition,
                [
                    'css_id' => 'some-id',
                    'compound' => true,
                    'inner' => 'inner-value',
                ],
            ),
        ];

        $compoundDefinition = $blockDefinition->getParameterDefinition('compound');

        self::assertCount(4, $mappedParameters);
        self::assertArrayHasKey('css_class', $mappedParameters);
        self::assertArrayHasKey('css_id', $mappedParameters);
        self::assertArrayHasKey('compound', $mappedParameters);
        self::assertArrayHasKey('inner', $mappedParameters);

        self::assertContainsOnlyInstancesOf(Parameter::class, $mappedParameters);

        self::assertSame($blockDefinition->getParameterDefinition('css_class'), $mappedParameters['css_class']->parameterDefinition);
        self::assertSame('some-class', $mappedParameters['css_class']->value);
        self::assertFalse($mappedParameters['css_class']->isEmpty);

        self::assertSame($blockDefinition->getParameterDefinition('css_id'), $mappedParameters['css_id']->parameterDefinition);
        self::assertSame('some-id', $mappedParameters['css_id']->value);
        self::assertFalse($mappedParameters['css_id']->isEmpty);

        self::assertSame($blockDefinition->getParameterDefinition('compound'), $mappedParameters['compound']->parameterDefinition);
        self::assertTrue($mappedParameters['compound']->value);
        self::assertFalse($mappedParameters['compound']->isEmpty);

        self::assertSame($compoundDefinition->getParameterDefinition('inner'), $mappedParameters['inner']->parameterDefinition);
        self::assertSame('inner-value', $mappedParameters['inner']->value);
        self::assertFalse($mappedParameters['inner']->isEmpty);
    }

    public function testSerializeValues(): void
    {
        $handler = new BlockDefinitionHandlerWithCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ],
        );

        $serializedParameters = $this->mapper->serializeValues(
            $blockDefinition,
            [
                'css_class' => 'some-class',
                'compound' => true,
                'inner' => 'inner-value',
            ],
            [
                'css_class' => null,
                'unknown' => 'value',
            ],
        );

        self::assertSame(
            [
                'css_class' => 'some-class',
                'unknown' => 'value',
                'compound' => true,
                'inner' => 'inner-value',
            ],
            [...$serializedParameters],
        );
    }

    public function testExtractUntranslatableParameters(): void
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $blockDefinition = BlockDefinition::fromArray(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ],
        );

        $untranslatableParams = $this->mapper->extractUntranslatableParameters(
            $blockDefinition,
            [
                'css_id' => 'some-id',
                'css_class' => 'some-class',
                'compound' => true,
                'inner' => 'inner-value',
            ],
        );

        self::assertSame(
            [
                'other' => null,
                'css_id' => 'some-id',
                'compound' => true,
                'inner' => 'inner-value',
            ],
            [...$untranslatableParams],
        );
    }
}
