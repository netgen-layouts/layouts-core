<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form;

use Netgen\Layouts\Form\AbstractType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AbstractTypeTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Form\AbstractType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve([]);

        self::assertSame(
            'ngbm_forms',
            $resolvedOptions['translation_domain']
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return $this->getMockForAbstractClass(AbstractType::class);
    }
}
