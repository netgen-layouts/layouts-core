<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType as LinkParameterType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\LinkType
     */
    private $parameterType;

    public function setUp()
    {
        $this->parameterType = new LinkParameterType(
            new ValueTypeRegistry(),
            new RemoteIdConverter($this->createMock(ItemLoaderInterface::class))
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new LinkType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->createMock(BackendInterface::class));

        return array(
            new ContentBrowserDynamicType(
                $backendRegistry,
                array('value')
            ),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'link_type' => 'url',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'http://www.google.com',
        );

        $formData = new LinkValue(
            array(
                'linkType' => 'url',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
                'link' => 'http://www.google.com',
            )
        );

        $parameterDefinition = new ParameterDefinition(
            array(
                'type' => $this->parameterType,
            )
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitInvalidData()
    {
        $submittedData = array(
            'link_type' => 'unknown',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'http://www.google.com',
        );

        $formData = new LinkValue();

        $parameterDefinition = new ParameterDefinition(
            array(
                'type' => $this->parameterType,
            )
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildView()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'type' => $this->parameterType,
            )
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            array(
                'link_type' => 'url',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'http://www.google.com',
            )
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        $errors = $form->get('url')->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('an error', iterator_to_array($errors)[0]->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildViewWithInvalidData()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'type' => $this->parameterType,
            )
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            array(
                'link_type' => 'unknown',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'http://www.google.com',
            )
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        $this->assertCount(0, $form->get('url')->getErrors());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'value_types' => array('value'),
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            array(
                'value_types' => array('value'),
                'translation_domain' => 'ngbm_forms',
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptionsWithEmptyValueTypes()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $resolvedOptions = $optionsResolver->resolve();

        $this->assertEquals(
            array(
                'value_types' => array(),
                'translation_domain' => 'ngbm_forms',
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "value_types" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'value_types' => 42,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_link', $this->formType->getBlockPrefix());
    }
}
