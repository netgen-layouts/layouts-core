<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\LayoutTypeNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\LayoutTypeFactory;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

#[CoversClass(LayoutTypeNormalizer::class)]
final class LayoutTypeNormalizerTest extends TestCase
{
    private LayoutTypeNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new LayoutTypeNormalizer();
        $this->normalizer->setNormalizer(new Serializer());
    }

    public function testNormalize(): void
    {
        $layoutType = LayoutTypeFactory::buildLayoutType(
            'test_layout_1',
            [
                'name' => 'Layout type',
                'icon' => '/icon.svg',
                'enabled' => true,
                'zones' => [
                    'zone1' => [
                        'name' => 'Zone 1',
                        'allowed_block_definitions' => ['title'],
                    ],
                    'zone2' => [
                        'name' => 'Zone 2',
                        'allowed_block_definitions' => [],
                    ],
                ],
            ],
        );

        self::assertSame(
            [
                'identifier' => 'test_layout_1',
                'name' => 'Layout type',
                'icon' => '/icon.svg',
                'zones' => [
                    [
                        'identifier' => 'zone1',
                        'name' => 'Zone 1',
                        'allowed_block_definitions' => ['title'],
                    ],
                    [
                        'identifier' => 'zone2',
                        'name' => 'Zone 2',
                        'allowed_block_definitions' => true,
                    ],
                ],
            ],
            $this->normalizer->normalize(new Value($layoutType)),
        );
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new LayoutType(), false],
            [new Value(new APIValue()), false],
            [new Value(new LayoutType()), true],
        ];
    }
}
