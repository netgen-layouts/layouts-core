<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

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
     * @doesNotPerformAssertions
     */
    public function testValidateLayoutCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
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
            $this->expectException(InvalidArgumentException::class);
        }

        $this->layoutValidator->validateLayoutUpdateStruct(
            new LayoutUpdateStruct($params)
        );
    }

    public function validateLayoutCreateStructDataProvider()
    {
        return array(
            array(array('type' => 'type', 'name' => 'Name', 'shared' => null), true),
            array(array('type' => 'type', 'name' => 'Name', 'shared' => false), true),
            array(array('type' => 'type', 'name' => 'Name', 'shared' => true), true),
            array(array('type' => null, 'name' => 'Name', 'shared' => null), false),
            array(array('type' => '', 'name' => 'Name', 'shared' => null), false),
            array(array('type' => 42, 'name' => 'Name', 'shared' => null), false),
            array(array('type' => 'type', 'name' => null, 'shared' => null), false),
            array(array('type' => 'type', 'name' => '', 'shared' => null), false),
            array(array('type' => 'type', 'name' => '   ', 'shared' => null), false),
            array(array('type' => 'type', 'name' => 42, 'shared' => null), false),
            array(array('type' => 'type', 'name' => 'Name', 'shared' => ''), false),
            array(array('type' => 'type', 'name' => 'Name', 'shared' => 42), false),
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
}
