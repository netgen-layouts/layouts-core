<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfo as PathInfoMapper;
use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PathInfoTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new PathInfo();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'path_info' => new PathInfoMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfo::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfo::getFormOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => '/some/path',
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = '/some/path';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
