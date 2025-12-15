<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType;

use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Layout\Resolver\Form\TargetType;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RoutePrefixMapper;
use Netgen\Layouts\Layout\Resolver\TargetType\RoutePrefix;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

#[CoversClass(TargetType::class)]
#[CoversClass(Mapper::class)]
#[CoversClass(RoutePrefixMapper::class)]
final class RoutePrefixTest extends FormTestCase
{
    private RoutePrefix $targetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetType = new RoutePrefix();
    }

    public function testSubmitValidData(): void
    {
        $submittedData = [
            'value' => 'route_prefix_',
        ];

        $struct = new TargetCreateStruct();

        $form = $this->factory->create(
            TargetType::class,
            $struct,
            ['target_type' => $this->targetType],
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        self::assertTrue($form->isSynchronized());

        self::assertSame('route_prefix_', $struct->value);

        $formView = $form->createView();

        self::assertArrayHasKey('value', $formView->children);

        self::assertArrayHasKey('target_type', $formView->vars);
        self::assertSame($this->targetType, $formView->vars['target_type']);
    }

    protected function getMainType(): FormTypeInterface
    {
        return new TargetType(
            new Container(
                [
                    'route_prefix' => new RoutePrefixMapper(),
                ],
            ),
        );
    }
}
