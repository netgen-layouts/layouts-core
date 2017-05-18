<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LayoutValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    protected $layoutValidator;

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
     * @doesNotPerformAssertions
     */
    public function testValidateLayoutCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $this->layoutValidator->validateLayoutCreateStruct(new LayoutCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutUpdateStruct
     * @dataProvider validateLayoutUpdateStructDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateLayoutUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

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
     * @doesNotPerformAssertions
     */
    public function testValidateLayoutCopyStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        $this->layoutValidator->validateLayoutCopyStruct(
            new LayoutCopyStruct($params)
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
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => false,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => true,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => null,
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => 42,
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => null,
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => '',
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => '   ',
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 42,
                    'description' => 'Description',
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => null,
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => '',
                    'shared' => null,
                ),
                true,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 42,
                    'shared' => null,
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
                    'shared' => '',
                ),
                false,
            ),
            array(
                array(
                    'layoutType' => $this->getLayoutType(),
                    'name' => 'Name',
                    'description' => 'Description',
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

    public function getLayoutType()
    {
        return new LayoutType(
            array(
                'identifier' => 'type',
            )
        );
    }
}
