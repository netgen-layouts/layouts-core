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

final class LayoutValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\Layouts\Core\Validator\LayoutValidator
     */
    private $layoutValidator;

    public function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->layoutValidator = new LayoutValidator();
        $this->layoutValidator->setValidator($this->validator);
    }

    /**
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutCreateStruct
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
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutUpdateStruct
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
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateLayoutCopyStruct
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
     * @covers \Netgen\Layouts\Core\Validator\LayoutValidator::validateChangeLayoutType
     * @dataProvider validateChangeLayoutTypeDataProvider
     */
    public function testValidateChangeLayoutType(array $zoneMapping): void
    {
        // Tests without assertions are not covered by PHPUnit, so we fake the assertion count
        $this->addToAssertionCount(1);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            $zoneMapping
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
            $this->getLayoutType(),
            ['left' => ['top', 'shared']],
            false
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
            $this->getLayoutType(),
            ['unknown' => []]
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
            $this->getLayoutType(),
            ['left' => 42]
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
            $this->getLayoutType(),
            ['left' => ['top'], 'right' => ['top']]
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
            $this->getLayoutType(),
            ['left' => ['unknown']]
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
            $this->getLayoutType(),
            ['left' => ['top', 'shared']]
        );
    }

    public function validateLayoutCreateStructDataProvider(): array
    {
        return [
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                true,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => false,
                ],
                true,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => true,
                ],
                true,
            ],
            [
                [
                    'layoutType' => null,
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => 42,
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => null,
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => '',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => '   ',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 42,
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => null,
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                true,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => '',
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                true,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 42,
                    'mainLocale' => 'en',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => '',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'unknown',
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => '',
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => 42,
                ],
                false,
            ],
        ];
    }

    public function validateLayoutUpdateStructDataProvider(): array
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
                    'name' => 23,
                ],
                false,
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
                    'description' => 23,
                ],
                false,
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

    public function validateLayoutCopyStructDataProvider(): array
    {
        return [
            [['name' => 'New name', 'description' => 'New description'], true],
            [['name' => 23, 'description' => 'New description'], false],
            [['name' => null, 'description' => 'New description'], false],
            [['name' => '', 'description' => 'New description'], false],
            [['name' => 'New name', 'description' => 23], false],
            [['name' => 'New name', 'description' => null], true],
            [['name' => 'New name', 'description' => ''], true],
        ];
    }

    public function validateChangeLayoutTypeDataProvider(): array
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
                    ]
                ),
            ]
        );
    }

    private function getLayoutType(): LayoutTypeInterface
    {
        return LayoutType::fromArray(
            [
                'identifier' => 'type',
                'zones' => [
                    'left' => new LayoutTypeZone(),
                    'right' => new LayoutTypeZone(),
                ],
            ]
        );
    }
}
