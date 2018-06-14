<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Extension;

use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\ParameterDefinition;
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

    public function setUp(): void
    {
        $this->formTypeExtension = new ParametersTypeExtension();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::getExtendedType
     */
    public function testGetExtendedType(): void
    {
        $this->assertEquals(FormType::class, $this->formTypeExtension->getExtendedType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildView(): void
    {
        $view = new FormView();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            ['ngbm_parameter_definition' => new ParameterDefinition()]
        );

        $this->assertArrayHasKey('ngbm_parameter_definition', $view->vars);
        $this->assertEquals(new ParameterDefinition(), $view->vars['ngbm_parameter_definition']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildViewWithEmptyOptions(): void
    {
        $view = new FormView();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            []
        );

        $this->assertArrayNotHasKey('ngbm_parameter_definition', $view->vars);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $options = [
            'ngbm_parameter_definition' => new ParameterDefinition(),
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            [
                'ngbm_parameter_definition' => new ParameterDefinition(),
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptionsWithEmptyParameters(): void
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve();

        $this->assertEquals([], $resolvedOptions);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "ngbm_parameter_definition" with value "parameter_definition" is expected to be of type "Netgen\BlockManager\Parameters\ParameterDefinition", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'ngbm_parameter_definition' => 'parameter_definition',
            ]
        );
    }
}
