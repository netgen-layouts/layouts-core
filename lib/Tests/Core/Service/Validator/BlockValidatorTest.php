<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\API\Values\Page\BlockUpdateStruct;
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => null,
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
                    'blockDefinition' => 42,
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
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
                    'blockDefinition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                ),
                false,
            ),
            array(
                array(
                    'blockDefinition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(),
                ),
                false,
            ),
            array(
                array(
                    'blockDefinition' => $this->getBlockDefinition(),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => 42,
                ),
                false,
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
     * @return \Netgen\BlockManager\Block\BlockDefinition
     */
    protected function getBlockDefinition()
    {
        $config = new Configuration(
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

        $handler = new BlockDefinitionHandlerWithRequiredParameter();

        return new BlockDefinition(
            array(
                'identifier' => 'block',
                'handler' => $handler,
                'config' => $config,
                'parameters' => $handler->getParameters(),
            )
        );
    }
}
