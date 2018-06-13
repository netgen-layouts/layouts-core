<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route as RouteMapper;
use Netgen\BlockManager\Layout\Resolver\TargetType\Route;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new Route();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            [
                'route' => new RouteMapper(),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildView
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route::getFormType
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'value' => 'route_name',
        ];

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = 'route_name';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            ['target_type' => $this->targetType]
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $formView = $form->createView();

        $this->assertArrayHasKey('value', $formView->children);

        $this->assertArrayHasKey('target_type', $formView->vars);
        $this->assertEquals($this->targetType, $formView->vars['target_type']);
    }
}
