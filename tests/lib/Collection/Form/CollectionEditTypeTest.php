<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Form;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Form\CollectionEditType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class CollectionEditTypeTest extends FormTestCase
{
    private Collection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection();
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::buildForm
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'offset' => 10,
            'limit' => 5,
        ];

        $struct = new CollectionUpdateStruct();

        $form = $this->factory->create(
            CollectionEditType::class,
            $struct,
            ['collection' => $this->collection],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame(5, $struct->limit);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        self::assertArrayHasKey('collection', $view->vars);
        self::assertSame($this->collection, $view->vars['collection']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::buildForm
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::buildView
     */
    public function testSubmitValidDataWithDynamicCollection(): void
    {
        $this->collection = Collection::fromArray(['query' => new Query()]);

        $submittedData = [
            'offset' => 10,
            'limit' => 5,
        ];

        $struct = new CollectionUpdateStruct();

        $form = $this->factory->create(
            CollectionEditType::class,
            $struct,
            ['collection' => $this->collection],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame(10, $struct->offset);
        self::assertSame(5, $struct->limit);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        self::assertArrayHasKey('collection', $view->vars);
        self::assertSame($this->collection, $view->vars['collection']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new CollectionUpdateStruct();

        $options = $optionsResolver->resolve(
            [
                'collection' => $this->collection,
                'data' => $struct,
            ],
        );

        self::assertSame($this->collection, $options['collection']);
        self::assertSame($struct, $options['data']);
        self::assertSame('nglayouts_forms', $options['translation_domain']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::configureOptions
     */
    public function testConfigureOptionsWithMissingQuery(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "collection" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidQueryType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "collection" with value "" is expected to be of type "Netgen\Layouts\API\Values\Collection\Collection", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'collection' => '',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\CollectionEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'collection' => $this->collection,
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CollectionEditType();
    }
}
