<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition as BlockDefinitionStub;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class BlockValidatorTest extends TestCase
{
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
        $this->blockDefinitionRegistryMock = $this->createMock(BlockDefinitionRegistryInterface::class);
        $this->blockDefinitionConfig = new Configuration(
            array(
                'identifier' => 'def',
                'forms' => array(),
                'viewTypes' => array(
                    'large' => new ViewType(
                        array(
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(
                                    array(
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

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
     * @dataProvider validateBlockCreateStructDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateBlockCreateStruct(array $params, $isValid)
    {
        $handler = new BlockDefinitionHandlerWithRequiredParameter();
        $blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block',
                'handler' => $handler,
                'config' => $this->blockDefinitionConfig,
                'parameters' => $handler->getParameters(),
            )
        );

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->will($this->returnValue($blockDefinition));

        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->blockValidator->validateBlockCreateStruct(new BlockCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockUpdateStruct
     * @dataProvider validateBlockUpdateStructDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateBlockUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->blockValidator->validateBlockUpdateStruct(
            new Block(
                array(
                    'viewType' => 'large',
                    'blockDefinition' => new BlockDefinitionStub(
                        'block_definition',
                        array('large' => array('standard'))
                    ),
                )
            ),
            new BlockUpdateStruct($params)
        );
    }

    public function validateBlockCreateStructDataProvider()
    {
        return array(
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => '',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => null,
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => '',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'definitionIdentifier' => 'block_definition',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => null,
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => null,
                    'itemViewType' => null,
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => '',
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => null,
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_id' => 'id',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                    ),
                ),
                true,
            ),
        );
    }
}
