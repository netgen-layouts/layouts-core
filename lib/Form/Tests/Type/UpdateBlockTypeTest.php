<?php

namespace Netgen\BlockManager\Form\Tests\Type;

use Netgen\BlockManager\BlockDefinition\Tests\Stubs\BlockDefinition;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Form\Type\UpdateBlockType;
use Netgen\BlockManager\Form\Data\UpdateBlockData;
use Symfony\Component\Form\Test\TypeTestCase;

class UpdateBlockTypeTest extends TypeTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockDefinitionRegistry = $this->getMock(
            'Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface'
        );

        $this->blockDefinitionRegistry
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinition()));

        $this->configuration = $this->getMock(
            'Netgen\BlockManager\Configuration\ConfigurationInterface'
        );

        $blockConfig = array(
            'view_types' => array(
                'large' => array('name' => 'Large'),
                'small' => array('name' => 'Small'),
            ),
        );

        $this->configuration
            ->expects($this->any())
            ->method('getBlockConfig')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockConfig));
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::__construct
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::getName
     */
    public function testGetName()
    {
        $form = new UpdateBlockType($this->blockDefinitionRegistry, $this->configuration);
        self::assertEquals('ngbm_block', $form->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::buildForm
     * @expectedException \RuntimeException
     */
    public function testBuildFormWithInvalidData()
    {
        $form = $this->factory->create(
            new UpdateBlockType(
                $this->blockDefinitionRegistry,
                $this->configuration
            ),
            array()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
            'view_type' => 'large',
        );

        $block = new Block(
            array(
                'definitionIdentifier' => 'block_definition',
            )
        );

        $blockUpdateStruct = new BlockUpdateStruct();
        $formData = new UpdateBlockData($block, $blockUpdateStruct);

        $updatedFormData = clone $formData;
        $updatedFormData->updateStruct = clone $formData->updateStruct;

        $updatedFormData->updateStruct->viewType = 'large';
        $updatedFormData->updateStruct->setParameter('css_id', 'Some CSS ID');
        $updatedFormData->updateStruct->setParameter('css_class', 'Some CSS class');

        $form = $this->factory->create(
            new UpdateBlockType(
                $this->blockDefinitionRegistry,
                $this->configuration
            ),
            $formData
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedFormData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
        }
    }
}
