<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form;

use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Layout\Resolver\Form\RuleType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

#[CoversClass(RuleType::class)]
final class RuleTypeTest extends FormTestCase
{
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'description' => 'New description',
        ];

        $struct = new RuleUpdateStruct();

        $form = $this->factory->create(RuleType::class, $struct);

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('New description', $struct->description);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new RuleUpdateStruct();

        $options = $optionsResolver->resolve(
            [
                'data' => $struct,
            ],
        );

        self::assertSame($struct, $options['data']);
    }

    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new RuleType();
    }
}
