<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(CompoundBooleanType::class)]
final class CompoundBooleanTypeOptionsTest extends FormTestCase
{
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'reverse' => true,
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertTrue($resolvedOptions['inherit_data']);
        self::assertTrue($resolvedOptions['reverse']);
    }

    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_compound_boolean', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CompoundBooleanType();
    }
}
