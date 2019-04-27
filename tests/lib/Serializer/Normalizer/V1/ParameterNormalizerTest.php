<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Normalizer\V1;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Netgen\Layouts\Serializer\Normalizer\V1\ParameterNormalizer;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ParameterNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Normalizer\V1\ParameterNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ParameterNormalizer();
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\ParameterNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $parameter = Parameter::fromArray(
            [
                'parameterDefinition' => ParameterDefinition::fromArray(
                    [
                        'type' => new TextType(),
                    ]
                ),
                'value' => 'some text',
            ]
        );

        self::assertSame(
            'some text',
            $this->normalizer->normalize(new VersionedValue($parameter, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Layouts\Serializer\Normalizer\V1\ParameterNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
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
