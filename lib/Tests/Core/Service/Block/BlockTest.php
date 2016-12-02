<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class BlockTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    public function setUp()
    {
        parent::setUp();

        $this->prepareParameterTypeRegistry();

        $validator = $this->getValidator();

        $blockValidator = new BlockValidator();
        $blockValidator->setValidator($validator);

        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($validator);

        $this->blockService = $this->createBlockService($blockValidator);
        $this->layoutService = $this->createLayoutService($layoutValidator);
    }

    /**
     * @param array $parameters
     * @param array $expectedParameters
     * @dataProvider parametersDataProvider
     */
    public function testCreateBlock(array $parameters, array $expectedParameters)
    {
        $blockDefinition = $this->createBlockDefinition();
        $blockType = new BlockType(
            array(
                'blockDefinition' => $blockDefinition,
            )
        );

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockType);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillValues($blockDefinition, $parameters);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $layout,
            'left'
        );

        $createdParameters = array();
        foreach ($createdBlock->getParameters() as $parameterName => $parameterValue) {
            $createdParameters[$parameterName] = $parameterValue->getValue();
        }

        $this->assertEquals($expectedParameters, $createdParameters);
    }

    /**
     * @param array $parameters
     * @dataProvider invalidParametersDataProvider
     * @expectedException \Netgen\BlockManager\Exception\ValidationFailedException
     */
    public function testCreateBlockWithInvalidParameters(array $parameters)
    {
        $blockDefinition = $this->createBlockDefinition();
        $blockType = new BlockType(
            array(
                'blockDefinition' => $blockDefinition,
            )
        );

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockType);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillValues($blockDefinition, $parameters);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->blockService->createBlock(
            $blockCreateStruct,
            $layout,
            'left'
        );
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    abstract public function createBlockDefinitionHandler();

    /**
     * @return array
     */
    abstract public function parametersDataProvider();

    /**
     * @return array
     */
    abstract public function invalidParametersDataProvider();

    /**
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes()
    {
        return array();
    }

    public function getValidators()
    {
        return array();
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected function createBlockDefinition()
    {
        $blockDefinition = BlockDefinitionFactory::buildBlockDefinition(
            'definition',
            $this->createBlockDefinitionHandler(),
            $this->createBlockConfiguration(),
            new ParameterBuilder(
                $this->parameterTypeRegistry
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition('definition', $blockDefinition);

        return $blockDefinition;
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected function createBlockConfiguration()
    {
        return new Configuration(
            array(
                'viewTypes' => array(
                    'default' => new ViewType(
                        array(
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(),
                            ),
                        )
                    ),
                ),
            )
        );
    }

    protected function prepareParameterTypeRegistry()
    {
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\UrlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\RangeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\NumberType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\LinkType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ItemLinkType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IdentifierType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\HtmlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\EmailType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ChoiceType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        foreach ($this->getParameterTypes() as $parameterType) {
            $this->parameterTypeRegistry->addParameterType($parameterType);
        }
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }
}
