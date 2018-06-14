<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Collection\Form\CollectionEditType;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CollectionEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection();
    }

    public function getMainType(): FormTypeInterface
    {
        return new CollectionEditType();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'offset' => 10,
            'limit' => 5,
        ];

        $updatedStruct = new CollectionUpdateStruct();
        $updatedStruct->limit = 5;

        $form = $this->factory->create(
            CollectionEditType::class,
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
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::buildView
     */
    public function testSubmitValidDataWithDynamicCollection(): void
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
            CollectionEditType::class,
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
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::configureOptions
     */
    public function testConfigureOptions(): void
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
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "collection" is missing.
     */
    public function testConfigureOptionsWithMissingQuery(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "collection" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\Collection", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidQueryType(): void
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
     * @covers \Netgen\BlockManager\Collection\Form\CollectionEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
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
