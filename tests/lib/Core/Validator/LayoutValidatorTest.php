<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Validator;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\API\Values\ZoneMappings;
use Netgen\Layouts\Core\Validator\LayoutValidator;
use Netgen\Layouts\Exception\API\LayoutException;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Layout\Type\Zone as LayoutTypeZone;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\VarExporter\Hydrator;

#[CoversClass(LayoutValidator::class)]
final class LayoutValidatorTest extends TestCase
{
    private LayoutValidator $layoutValidator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ValidatorFactory(
                    self::createStub(LayoutService::class),
                    self::createStub(LayoutResolverService::class),
                    self::createStub(CmsItemLoaderInterface::class),
                ),
            )
            ->getValidator();

        $this->layoutValidator = new LayoutValidator();
        $this->layoutValidator->setValidator($validator);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateLayoutCreateStructDataProvider')]
    public function testValidateLayoutCreateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new LayoutCreateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutValidator->validateLayoutCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateLayoutUpdateStructDataProvider')]
    public function testValidateLayoutUpdateStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new LayoutUpdateStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutValidator->validateLayoutUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('validateLayoutCopyStructDataProvider')]
    public function testValidateLayoutCopyStruct(array $params, bool $isValid): void
    {
        $isValid ?
            $this->expectNotToPerformAssertions() :
            $this->expectException(ValidationException::class);

        $struct = new LayoutCopyStruct();
        Hydrator::hydrate($struct, $params);

        $this->layoutValidator->validateLayoutCopyStruct($struct);
    }

    #[DataProvider('validateChangeLayoutTypeDataProvider')]
    #[DoesNotPerformAssertions]
    public function testValidateChangeLayoutType(ZoneMappings $zoneMappings): void
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            $zoneMappings,
        );
    }

    #[DoesNotPerformAssertions]
    public function testValidateChangeLayoutTypeWhenNotPreservingSharedZones(): void
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            new ZoneMappings()->addZoneMapping('left', ['top', 'shared']),
            false,
        );
    }

    public function testValidateChangeLayoutTypeWithNonExistingZoneInLayoutType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Zone "unknown" does not exist in "type" layout type.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            new ZoneMappings()->addZoneMapping('unknown', []),
        );
    }

    public function testValidateChangeLayoutTypeWithDuplicateZones(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Zone "top" is specified more than once.');

        $zoneMappings = new ZoneMappings()
            ->addZoneMapping('left', ['top'])
            ->addZoneMapping('right', ['top']);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            $zoneMappings,
        );
    }

    public function testValidateChangeLayoutTypeWithNonExistingLayoutZone(): void
    {
        $this->expectException(LayoutException::class);
        $this->expectExceptionMessage('Zone with "unknown" identifier does not exist in the layout.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            new ZoneMappings()->addZoneMapping('left', ['unknown']),
        );
    }

    public function testValidateChangeLayoutTypeWithNonOneOnOneSharedZoneMapping(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('When preserving shared layout zones, mapping for zone "left" needs to be 1:1.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            new ZoneMappings()->addZoneMapping('left', ['top', 'shared']),
        );
    }

    public static function validateLayoutCreateStructDataProvider(): iterable
    {
        return [
            [
                [
                    'uuid' => null,
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'isShared' => false,
                ],
                false,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'isShared' => false,
                ],
                false,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'isShared' => false,
                ],
                false,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'isShared' => false,
                ],
                true,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'isShared' => true,
                ],
                true,
            ],
        ];
    }

    public static function validateLayoutUpdateStructDataProvider(): iterable
    {
        return [
            [
                [
                    'name' => 'New name',
                ],
                true,
            ],
            [
                [
                    'name' => null,
                ],
                true,
            ],
            [
                [
                    'name' => '',
                ],
                false,
            ],
            [
                [
                    'name' => '   ',
                ],
                false,
            ],
            [
                [
                    'description' => 'New description',
                ],
                true,
            ],
            [
                [
                    'description' => null,
                ],
                true,
            ],
            [
                [
                    'description' => '',
                ],
                true,
            ],
            [
                [
                    'description' => '   ',
                ],
                true,
            ],
        ];
    }

    public static function validateLayoutCopyStructDataProvider(): iterable
    {
        return [
            [['description' => 'New description'], false],
            [['name' => 'New name', 'description' => 'New description'], true],
            [['name' => '', 'description' => 'New description'], false],
            [['name' => 'New name', 'description' => null], true],
            [['name' => 'New name', 'description' => ''], true],
        ];
    }

    public static function validateChangeLayoutTypeDataProvider(): iterable
    {
        return [
            [
                new ZoneMappings()->addZoneMapping('left', ['top']),
            ],
            [
                new ZoneMappings()->addZoneMapping('left', ['shared']),
            ],
            [
                new ZoneMappings()->addZoneMapping('left', ['top', 'bottom']),
            ],
            [
                new ZoneMappings()->addZoneMapping('left', ['top'])->addZoneMapping('right', ['bottom']),
            ],
            [
                new ZoneMappings()->addZoneMapping('left', [])->addZoneMapping('right', []),
            ],
            [
                new ZoneMappings()->addZoneMapping('left', []),
            ],
            [
                new ZoneMappings(),
            ],
        ];
    }

    private function getLayout(): Layout
    {
        return Layout::fromArray(
            [
                'zones' => ZoneList::fromArray(
                    [
                        'top' => Zone::fromArray(['linkedZone' => null]),
                        'bottom' => Zone::fromArray(['linkedZone' => null]),
                        'shared' => Zone::fromArray(['linkedZone' => new Zone()]),
                    ],
                ),
            ],
        );
    }

    private static function getLayoutType(): LayoutTypeInterface
    {
        return LayoutType::fromArray(
            [
                'identifier' => 'type',
                'zones' => [
                    'left' => new LayoutTypeZone(),
                    'right' => new LayoutTypeZone(),
                ],
            ],
        );
    }
}
