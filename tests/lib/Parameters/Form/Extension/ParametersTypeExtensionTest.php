<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Extension;

use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersTypeExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension
     */
    private $formTypeExtension;

    public function setUp(): void
    {
        $this->formTypeExtension = new ParametersTypeExtension();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::getExtendedType
     */
    public function testGetExtendedType(): void
    {
        self::assertSame(FormType::class, $this->formTypeExtension->getExtendedType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::getExtendedTypes
     */
    public function testGetExtendedTypes(): void
    {
        self::assertSame([FormType::class], $this->formTypeExtension::getExtendedTypes());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildView(): void
    {
        $view = new FormView();

        $parameterDefinition = new ParameterDefinition();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            ['ngbm_parameter_definition' => $parameterDefinition]
        );

        self::assertArrayHasKey('ngbm_parameter_definition', $view->vars);
        self::assertSame($parameterDefinition, $view->vars['ngbm_parameter_definition']);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::buildView
     */
    public function testBuildViewWithEmptyOptions(): void
    {
        $view = new FormView();

        $this->formTypeExtension->buildView(
            $view,
            $this->createMock(FormInterface::class),
            []
        );

        self::assertArrayNotHasKey('ngbm_parameter_definition', $view->vars);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $parameterDefinition = new ParameterDefinition();

        $options = [
            'ngbm_parameter_definition' => $parameterDefinition,
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertSame(
            [
                'ngbm_parameter_definition' => $parameterDefinition,
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptionsWithEmptyParameters(): void
    {
        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve();

        self::assertSame([], $resolvedOptions);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension::configureOptions
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "ngbm_parameter_definition" with value "parameter_definition" is expected to be of type "Netgen\\Layouts\\Parameters\\ParameterDefinition", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $this->formTypeExtension->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'ngbm_parameter_definition' => 'parameter_definition',
            ]
        );
    }
}
