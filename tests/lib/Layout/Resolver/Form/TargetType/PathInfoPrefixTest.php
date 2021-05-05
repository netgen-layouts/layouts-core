<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType;

use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Layout\Resolver\Form\TargetType;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix as PathInfoPrefixMapper;
use Netgen\Layouts\Layout\Resolver\TargetType\PathInfoPrefix;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

final class PathInfoPrefixTest extends FormTestCase
{
    private PathInfoPrefix $targetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetType = new PathInfoPrefix();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::buildView
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'value' => '/some/route',
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

        self::assertSame('/some/route', $struct->value);

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
                    'path_info_prefix' => new PathInfoPrefixMapper(),
                ],
            ),
        );
    }
}
