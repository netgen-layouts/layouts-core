<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition as BlockDefinitionStub;
use Symfony\Component\Validator\Validation;

class BlockValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionHandlerMock;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $blockDefinitionConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\BlockValidator
     */
    protected $blockValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->blockDefinitionRegistryMock = $this->getMock(BlockDefinitionRegistryInterface::class);
        $this->blockDefinitionHandlerMock = $this->getMock(BlockDefinitionHandlerInterface::class);
        $this->blockDefinitionConfig = new Configuration('def', array(), array('large' => array('name' => 'Large')));

        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $this->blockValidator = new BlockValidator($this->blockDefinitionRegistryMock);
        $this->blockValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockCreateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     * @dataProvider validateBlockCreateStructDataProvider
     */
    public function testValidateBlockCreateStruct(array $params, $isValid)
    {
        $blockDefinition = new BlockDefinitionWithRequiredParameter(
            'block',
            $this->blockDefinitionHandlerMock,
            $this->blockDefinitionConfig
        );

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->will($this->returnValue($blockDefinition));

        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->blockValidator->validateBlockCreateStruct(new BlockCreateStruct($params))
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockUpdateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     * @dataProvider validateBlockUpdateStructDataProvider
     */
    public function testValidateBlockUpdateStruct(array $params, $isValid)
    {
        $blockDefinition = new BlockDefinitionStub(
            'block',
            $this->blockDefinitionHandlerMock,
            $this->blockDefinitionConfig
        );

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->will($this->returnValue($blockDefinition));

        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->blockValidator->validateBlockUpdateStruct(
                new Block(array('definitionIdentifier' => 'block_definition')),
                new BlockUpdateStruct($params)
            )
        );
    }

    public function validateBlockCreateStructDataProvider()
    {
        return array(
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definitionIdentifier' => null,
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => '',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 42,
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => null,
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => '',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 42,
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => null,
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => '',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 42,
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => '',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => null,
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                    ),
                ),
                true,
            ),
        );
    }

    public function validateBlockUpdateStructDataProvider()
    {
        return array(
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => null,
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => '',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 42,
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => null,
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => '',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 42,
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => '',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => null,
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                        'css_id' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'class',
                    ),
                ),
                true,
            ),
        );
    }
}
