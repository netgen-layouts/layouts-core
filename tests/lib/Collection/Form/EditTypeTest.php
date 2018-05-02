<?php

namespace Netgen\BlockManager\Tests\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Collection\Form\EditType;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new EditType();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\EditType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'offset' => 10,
            'limit' => 5,
        ];

        $updatedStruct = new CollectionUpdateStruct();
        $updatedStruct->limit = 5;

        $form = $this->factory->create(
            EditType::class,
            new CollectionUpdateStruct(),
            ['collection' => $this->collection]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        $this->assertArrayHasKey('collection', $view->vars);
        $this->assertEquals($this->collection, $view->vars['collection']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\EditType::buildView
     */
    public function testSubmitValidDataWithDynamicCollection()
    {
        $this->collection = new Collection(['query' => new Query()]);

        $submittedData = [
            'offset' => 10,
            'limit' => 5,
        ];

        $updatedStruct = new CollectionUpdateStruct();
        $updatedStruct->offset = 10;
        $updatedStruct->limit = 5;

        $form = $this->factory->create(
            EditType::class,
            new CollectionUpdateStruct(),
            ['collection' => $this->collection]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        $this->assertArrayHasKey('collection', $view->vars);
        $this->assertEquals($this->collection, $view->vars['collection']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'collection' => $this->collection,
                'data' => new CollectionUpdateStruct(),
            ]
        );

        $this->assertEquals($this->collection, $options['collection']);
        $this->assertEquals(new CollectionUpdateStruct(), $options['data']);
        $this->assertEquals('ngbm_forms', $options['translation_domain']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "collection" is missing.
     */
    public function testConfigureOptionsWithMissingQuery()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "collection" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\Collection", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidQueryType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'collection' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'collection' => $this->collection,
                'data' => '',
            ]
        );
    }
}
