<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix as RoutePrefixMapper;
use Netgen\BlockManager\Layout\Resolver\TargetType\RoutePrefix;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

final class RoutePrefixTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    public function setUp(): void
    {
        parent::setUp();

        $this->targetType = new RoutePrefix();
    }

    public function getMainType(): FormTypeInterface
    {
        return new TargetType(
            [
                'route_prefix' => new RoutePrefixMapper(),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildView
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'value' => 'route_prefix_',
        ];

        $struct = new TargetCreateStruct();

        $form = $this->factory->create(
            TargetType::class,
            $struct,
            ['target_type' => $this->targetType]
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());

        $this->assertSame('route_prefix_', $struct->value);

        $formView = $form->createView();

        $this->assertArrayHasKey('value', $formView->children);

        $this->assertArrayHasKey('target_type', $formView->vars);
        $this->assertSame($this->targetType, $formView->vars['target_type']);
    }
}
