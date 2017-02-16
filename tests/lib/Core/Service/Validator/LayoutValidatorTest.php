<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Page\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\LayoutUpdateStruct;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\ValidationFailedException;
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
            $this->expectException(ValidationFailedException::class);
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
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutValidator->validateLayoutUpdateStruct(
            new LayoutUpdateStruct($params)
        );
    }

    /**
     * @param string $layoutName
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutName
     * @dataProvider validateLayoutNameDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateLayoutName($layoutName, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->layoutValidator->validateLayoutName($layoutName, 'name');
    }

    public function validateLayoutCreateStructDataProvider()
    {
        return array(
            array(array('layoutType' => $this->getLayoutType(), 'name' => 'Name', 'shared' => null), true),
            array(array('layoutType' => $this->getLayoutType(), 'name' => 'Name', 'shared' => false), true),
            array(array('layoutType' => $this->getLayoutType(), 'name' => 'Name', 'shared' => true), true),
            array(array('layoutType' => null, 'name' => 'Name', 'shared' => null), false),
            array(array('layoutType' => 42, 'name' => 'Name', 'shared' => null), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => null, 'shared' => null), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => '', 'shared' => null), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => '   ', 'shared' => null), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => 42, 'shared' => null), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => 'Name', 'shared' => ''), false),
            array(array('layoutType' => $this->getLayoutType(), 'name' => 'Name', 'shared' => 42), false),
        );
    }

    public function validateLayoutUpdateStructDataProvider()
    {
        return array(
            array(array('name' => 'New name'), true),
            array(array('name' => 23), false),
            array(array('name' => null), false),
            array(array('name' => ''), false),
            array(array('name' => '   '), false),
        );
    }

    public function validateLayoutNameDataProvider()
    {
        return array(
            array('New name', true),
            array(23, false),
            array(null, false),
            array('', false),
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
