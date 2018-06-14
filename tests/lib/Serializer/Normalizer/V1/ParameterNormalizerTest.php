<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Serializer\Normalizer\V1\ParameterNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ParameterNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new ParameterNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $parameter = new Parameter(
            [
                'parameterDefinition' => new ParameterDefinition(
                    [
                        'type' => new TextType(),
                    ]
                ),
                'value' => 'some text',
            ]
        );

        $this->assertEquals(
            'some text',
            $this->normalizer->normalize(new VersionedValue($parameter, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['value', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Parameter(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Parameter(), 2), false],
            [new VersionedValue(new Parameter(), 1), true],
        ];
    }
}
