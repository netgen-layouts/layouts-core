<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone as LayoutTypeZone;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LayoutValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    private $layoutValidator;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->layoutValidator = new LayoutValidator();
        $this->layoutValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutCreateStruct
     * @dataProvider validateLayoutCreateStructDataProvider
     */
    public function testValidateLayoutCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutValidator->validateLayoutCreateStruct(new LayoutCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutUpdateStruct
     * @dataProvider validateLayoutUpdateStructDataProvider
     */
    public function testValidateLayoutUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutValidator->validateLayoutUpdateStruct(
            new LayoutUpdateStruct($params)
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutCopyStruct
     * @dataProvider validateLayoutCopyStructDataProvider
     */
    public function testValidateLayoutCopyStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutValidator->validateLayoutCopyStruct(
            new LayoutCopyStruct($params)
        );
    }

    /**
     * @param array $zoneMapping
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @dataProvider validateChangeLayoutTypeDataProvider
     */
    public function testValidateChangeLayoutType(array $zoneMapping)
    {
        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            $zoneMapping
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     */
    public function testValidateChangeLayoutTypeWhenNotPreservingSharedZones()
    {
        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['left' => ['top', 'shared']],
            false
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Zone "unknown" does not exist in "type" layout type.
     */
    public function testValidateChangeLayoutTypeWithNonExistingZoneInLayoutType()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['unknown' => []]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage The list of mapped zones for "left" zone must be an array.
     */
    public function testValidateChangeLayoutTypeWithInvalidMappedZones()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['left' => 42]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Zone "top" is specified more than once.
     */
    public function testValidateChangeLayoutTypeWithDuplicateZones()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['left' => ['top'], 'right' => ['top']]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Zone "unknown" does not exist in specified layout.
     */
    public function testValidateChangeLayoutTypeWithNonExistingLayoutZone()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['left' => ['unknown']]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateChangeLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage When preserving shared layout zones, mapping for zone "left" needs to be 1:1.
     */
    public function testValidateChangeLayoutTypeWithNonOneOnOneSharedZoneMapping()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            ['left' => ['top', 'shared']]
        );
    }

    public function validateLayoutCreateStructDataProvider()
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
                    'mainLocale' => null,
                    'shared' => null,
                ],
                false,
            ],
            [
                [
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 42,
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

    public function validateLayoutUpdateStructDataProvider()
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

    public function validateLayoutCopyStructDataProvider()
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

    public function validateChangeLayoutTypeDataProvider()
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

    public function getLayout()
    {
        return new Layout(
            [
                'zones' => new ArrayCollection(
                    [
                        'top' => new Zone(),
                        'bottom' => new Zone(),
                        'shared' => new Zone(['linkedZone' => new Zone()]),
                    ]
                ),
            ]
        );
    }

    public function getLayoutType()
    {
        return new LayoutType(
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
