<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Extension;

use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersTypeExtensionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension
     */
    private $formTypeExtension;

    public function setUp()
    {
        $this->formTypeExtension = new ParametersTypeExtension();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::getExtendedType
     */
    public function testGetExtendedType()
    {
        $this->assertEquals(FormType::class, $this->formTypeExtension->getExtendedType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildView()
    {
        $view = new FormView();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            array('ngbm_parameter' => new Parameter())
        );

        $this->assertArrayHasKey('ngbm_parameter', $view->vars);
        $this->assertEquals(new Parameter(), $view->vars['ngbm_parameter']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildViewWithEmptyOptions()
    {
        $view = new FormView();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            array()
        );

        $this->assertArrayNotHasKey('ngbm_parameter', $view->vars);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $options = array(
            'ngbm_parameter' => new Parameter(),
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            array(
                'ngbm_parameter' => new Parameter(),
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptionsWithEmptyParameters()
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve();

        $this->assertEquals(array(), $resolvedOptions);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'ngbm_parameter' => 'parameter',
            )
        );
    }
}
