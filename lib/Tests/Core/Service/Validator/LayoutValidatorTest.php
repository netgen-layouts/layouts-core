<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Validator\ValidatorFactory;
use Symfony\Component\Validator\Validation;

class LayoutValidatorTest extends \PHPUnit_Framework_TestCase
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
            ->setConstraintValidatorFactory(new ValidatorFactory())
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutValidator->validateLayoutCreateStruct(new LayoutCreateStruct($params))
        );
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
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->layoutValidator->validateLayoutUpdateStruct(
                new LayoutUpdateStruct($params)
            )
        );
    }

    public function validateLayoutCreateStructDataProvider()
    {
        return array(
            array(array('type' => 'type', 'name' => 'Name'), true),
            array(array('type' => null, 'name' => 'Name'), false),
            array(array('type' => '', 'name' => 'Name'), false),
            array(array('type' => 42, 'name' => 'Name'), false),
            array(array('type' => 'type', 'name' => null), false),
            array(array('type' => 'type', 'name' => ''), false),
            array(array('type' => 'type', 'name' => 42), false),
        );
    }

    public function validateLayoutUpdateStructDataProvider()
    {
        return array(
            array(array('name' => 'New name'), true),
            array(array('name' => 23), false),
            array(array('name' => null), false),
            array(array('name' => ''), false),
        );
    }
}
