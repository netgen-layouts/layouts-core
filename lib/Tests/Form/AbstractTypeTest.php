<?php

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return $this->getMockForAbstractClass(AbstractType::class);
    }

    /**
     * @covers \Netgen\BlockManager\Form\AbstractType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve(array());

        $this->assertEquals(
            AbstractType::TRANSLATION_DOMAIN,
            $resolvedOptions['translation_domain']
        );
    }
}
