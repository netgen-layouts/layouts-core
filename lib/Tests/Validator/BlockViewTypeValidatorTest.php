<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\BlockViewTypeValidator;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        parent::setUp();

        $this->blockDefinition = new BlockDefinition(
            'block',
            array('large' => array())
        );

        $this->constraint = new BlockViewType(array('definition' => $this->blockDefinition));
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return new BlockViewTypeValidator();
    }

    /**
     * @param string $viewType
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\BlockViewTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($viewType, $isValid)
    {
        $this->assertValid($isValid, $viewType);
    }

    public function validateDataProvider()
    {
        return array(
            array('large', true),
            array('small', false),
            array('', false),
        );
    }
}
