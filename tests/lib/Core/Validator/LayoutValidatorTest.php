<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Core\Validator\LayoutValidator;
use Netgen\Layouts\Exception\API\LayoutException;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Layout\Type\Zone as LayoutTypeZone;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use Netgen\Layouts\Utils\Hydrator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LayoutValidatorTest extends TestCase
{
    private ValidatorInterface $validator;

    private LayoutValidator $layoutValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->layoutValidator = new LayoutValidator();
        $this->layoutValidator->setValidator($this->validator);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutCreateStruct
     *
     * @dataProvider validateLayoutCreateStructDataProvider
     */
    public function testValidateLayoutCreateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new LayoutCreateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateLayoutCreateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutUpdateStruct
     *
     * @dataProvider validateLayoutUpdateStructDataProvider
     */
    public function testValidateLayoutUpdateStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new LayoutUpdateStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateLayoutUpdateStruct($struct);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutCopyStruct
     *
     * @dataProvider validateLayoutCopyStructDataProvider
     */
    public function testValidateLayoutCopyStruct(array $params, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $struct = new LayoutCopyStruct();
        (new Hydrator())->hydrate($params, $struct);

        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateLayoutCopyStruct($struct);
    }

    /**
     * @param array<string, string[]> $zoneMapping
     *
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     *
     * @dataProvider validateChangeLayoutTypeDataProvider
     */
    public function testValidateChangeLayoutType(array $zoneMapping): void
    {
        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            $zoneMapping,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWhenNotPreservingSharedZones(): void
    {
        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['left' => ['top', 'shared']],
            false,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWithNonExistingZoneInLayoutType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Zone "unknown" does not exist in "type" layout type.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['unknown' => []],
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWithInvalidMappedZones(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The list of mapped zones for "left" zone must be an array.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['left' => 42],
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWithDuplicateZones(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Zone "top" is specified more than once.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['left' => ['top'], 'right' => ['top']],
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWithNonExistingLayoutZone(): void
    {
        $this->expectException(LayoutException::class);
        $this->expectExceptionMessage('Zone with "unknown" identifier does not exist in the layout.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['left' => ['unknown']],
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWithNonOneOnOneSharedZoneMapping(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('When preserving shared layout zones, mapping for zone "left" needs to be 1:1.');

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            self::getLayoutType(),
            ['left' => ['top', 'shared']],
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
                    'shared' => false,
                ],
                false,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => false,
                ],
                false,
            ],
            [
                [
                    'uuid' => null,
                    'layoutType' => self::getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => false,
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
                    'shared' => false,
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
                    'shared' => true,
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
                [
                    'left' => ['top'],
                ],
            ],
            [
                [
                    'left' => ['shared'],
                ],
            ],
            [
                [
                    'left' => ['top', 'bottom'],
                ],
            ],
            [
                [
                    'left' => ['top'],
                    'right' => ['bottom'],
                ],
            ],
            [
                [
                    'left' => [],
                    'right' => [],
                ],
            ],
            [
                [
                    'left' => [],
                ],
            ],
            [
                [],
            ],
        ];
    }

    private function getLayout(): Layout
    {
        return Layout::fromArray(
            [
                'zones' => new ArrayCollection(
                    [
                        'top' => new Zone(),
                        'bottom' => new Zone(),
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
