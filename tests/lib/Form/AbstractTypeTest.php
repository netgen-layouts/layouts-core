<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AbstractTypeTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return $this->getMockForAbstractClass(AbstractType::class);
    }

    /**
     * @covers \Netgen\BlockManager\Form\AbstractType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve([]);

        $this->assertSame(
            'ngbm_forms',
            $resolvedOptions['translation_domain']
        );
    }
}
