<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
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

        $config = new Configuration(
            'block',
            array(),
            array(
                'large' => new ViewType('large', 'Large'),
            )
        );

        $this->blockDefinition = new BlockDefinition(
            'block',
            new BlockDefinitionHandler(),
            $config
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
