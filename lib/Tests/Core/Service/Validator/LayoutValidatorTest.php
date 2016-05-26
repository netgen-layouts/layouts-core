<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\InvalidArgumentException;
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
        $this->validator = Validation::createValidator();
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

    public function validateLayoutCreateStructDataProvider()
    {
        return array(
            array(array('type' => 'type', 'name' => 'Name', 'status' => Layout::STATUS_DRAFT), true),
            array(array('type' => null, 'name' => 'Name', 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => '', 'name' => 'Name', 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => 42, 'name' => 'Name', 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => 'type', 'name' => null, 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => 'type', 'name' => '', 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => 'type', 'name' => 42, 'status' => Layout::STATUS_DRAFT), false),
            array(array('type' => 'type', 'name' => 'Name', 'status' => null), false),
            array(array('type' => 'type', 'name' => 'Name', 'status' => 'draft'), false),
        );
    }
}
