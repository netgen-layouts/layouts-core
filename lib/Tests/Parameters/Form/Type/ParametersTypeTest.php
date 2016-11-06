<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametersTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new TextLineMapper());

        return new ParametersType($formMapperRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'parameters',
            ParametersType::class,
            array(
                'parameters' => array(
                    'css_class' => new Parameter('css_class', new ParameterType\TextLineType()),
                    'css_id' => new Parameter('css_id', new ParameterType\TextLineType()),
                ),
                'label_prefix' => 'label',
                'property_path' => 'parameters',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $this->assertCount(2, $parentForm->get('parameters')->all());

        foreach (array_keys($submittedData['parameters']) as $key) {
            $paramForm = $parentForm->get('parameters')->get($key);

            $this->assertEquals('parameters[' . $key . ']', $paramForm->getPropertyPath());
            $this->assertEquals('label.' . $key, $paramForm->getConfig()->getOption('label'));
            $this->assertInstanceOf(TextType::class, $paramForm->getConfig()->getType()->getInnerType());
        }

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('parameters', $children);

        foreach (array_keys($submittedData['parameters']) as $key) {
            $this->assertArrayHasKey($key, $children['parameters']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'parameters' => array(),
            'label_prefix' => 'label',
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            array(
                'parameters' => array(),
                'label_prefix' => 'label',
                'inherit_data' => true,
                'translation_domain' => ParametersType::TRANSLATION_DOMAIN,
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameters' => null,
                'label_prefix' => 'label',
            )
        );
    }
}
