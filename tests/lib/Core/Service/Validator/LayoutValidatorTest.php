<?php

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

    /**
     * Sets up the test.
     */
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
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Zone "unknown" does not exist in "type" layout type.
     */
    public function testValidateChangeLayoutTypeWithNonExistingZoneInLayoutType()
    {
        $this->layoutValidator->validateChangeLayoutType(
            $this->getLayout(),
            $this->getLayoutType(),
            array('unknown' => array())
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
            array('left' => 42)
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
            array('left' => array('top'), 'right' => array('top'))
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
            array('left' => array('unknown'))
        );
    }

    public function validateLayoutCreateStructDataProvider()
    {
        return array(
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => false,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => true,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => null,
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => 42,
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => null,
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => '',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => '   ',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 42,
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => null,
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => '',
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 42,
                    'mainLocale' => 'en',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => '',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => null,
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 42,
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'unknown',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => '',
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'mainLocale' => 'en',
                    'shared' => 42,
                ),
                false,
            ),
        );
    }

    public function validateLayoutUpdateStructDataProvider()
    {
        return array(
            array(
                array(
                    'name' => 'New name',
                ),
                true,
            ),
            array(
                array(
                    'name' => 23,
                ),
                false,
            ),
            array(
                array(
                    'name' => null,
                ),
                true,
            ),
            array(
                array(
                    'name' => '',
                ),
                false,
            ),
            array(
                array(
                    'name' => '   ',
                ),
                false,
            ),
            array(
                array(
                    'description' => 'New description',
                ),
                true,
            ),
            array(
                array(
                    'description' => 23,
                ),
                false,
            ),
            array(
                array(
                    'description' => null,
                ),
                true,
            ),
            array(
                array(
                    'description' => '',
                ),
                true,
            ),
            array(
                array(
                    'description' => '   ',
                ),
                true,
            ),
        );
    }

    public function validateLayoutCopyStructDataProvider()
    {
        return array(
            array(array('name' => 'New name', 'description' => 'New description'), true),
            array(array('name' => 23, 'description' => 'New description'), false),
            array(array('name' => null, 'description' => 'New description'), false),
            array(array('name' => '', 'description' => 'New description'), false),
            array(array('name' => 'New name', 'description' => 23), false),
            array(array('name' => 'New name', 'description' => null), true),
            array(array('name' => 'New name', 'description' => ''), true),
        );
    }

    public function validateChangeLayoutTypeDataProvider()
    {
        return array(
            array(
                array(
                    'left' => array('top'),
                ),
            ),
            array(
                array(
                    'left' => array('top', 'bottom'),
                ),
            ),
            array(
                array(
                    'left' => array('top'),
                    'right' => array('bottom'),
                ),
            ),
            array(
                array(
                    'left' => array(),
                    'right' => array(),
                ),
            ),
            array(
                array(
                    'left' => array(),
                ),
            ),
            array(
                array(),
            ),
        );
    }

    public function getLayout()
    {
        return new Layout(
            array(
                'zones' => new ArrayCollection(
                    array(
                        'top' => new Zone(),
                        'bottom' => new Zone(),
                    )
                ),
            )
        );
    }

    public function getLayoutType()
    {
        return new LayoutType(
            array(
                'identifier' => 'type',
                'zones' => array(
                    'left' => new LayoutTypeZone(),
                    'right' => new LayoutTypeZone(),
                ),
            )
        );
    }
}
