<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition as BlockDefinitionStub;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinition as ContainerDefinitionStub;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class BlockValidatorTest extends TestCase
{
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
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->blockValidator = new BlockValidator();
        $this->blockValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validateBlockCreateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\BlockValidator::validatePlaceholderCreateStruct
     * @dataProvider validateBlockCreateStructDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateBlockCreateStruct(array $params, $isValid)
    {
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
                    'definition' => new BlockDefinitionStub(
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => null,
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
                    'definition' => 42,
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
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
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                ),
                false,
            ),
            array(
                array(
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(),
                ),
                false,
            ),
            array(
                array(
                    'definition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => 42,
                ),
                false,
            ),

            // Container block definitions

            array(
                array(
                    'definition' => $this->getContainerDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ),
                    'placeholderStructs' => array(
                        'main' => new PlaceholderCreateStruct(
                            array(
                                'parameterValues' => array(
                                    'css_class' => 'class',
                                    'css_id' => 'id',
                                ),
                            )
                        ),
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
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(),
                ),
                true,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => 42,
                ),
                false,
            ),
            array(
                array(
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                ),
                true,
            ),
        );
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected function getBlockDefinition()
    {
        $handler = new BlockDefinitionHandlerWithRequiredParameter();

        return new BlockDefinitionStub(
            'block',
            array('large' => array('standard')),
            $handler
        );
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected function getContainerDefinition()
    {
        $handler = new ContainerDefinitionHandler(array(), array('main'));

        return new ContainerDefinitionStub(
            'block',
            array('large' => array('standard')),
            $handler
        );
    }
}
