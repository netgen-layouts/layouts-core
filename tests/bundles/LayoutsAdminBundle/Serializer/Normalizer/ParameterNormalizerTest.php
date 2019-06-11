<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ParameterNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;

final class ParameterNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ParameterNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ParameterNormalizer();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ParameterNormalizer::normalize
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
            $this->normalizer->normalize(new Value($parameter))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ParameterNormalizer::supportsNormalization
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
            [new APIValue(), false],
            [new Parameter(), false],
            [new Value(new APIValue()), false],
            [new Value(new Parameter()), true],
        ];
    }
}
