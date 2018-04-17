<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class TranslatableParameterBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory
     */
    private $factory;

    /**
     * @var \Netgen\BlockManager\Parameters\TranslatableParameterBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->registry = new ParameterTypeRegistry();
        $this->registry->addParameterType(new ParameterType\TextType());
        $this->registry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->factory = new TranslatableParameterBuilderFactory($this->registry);

        $this->builder = $this->factory->createParameterBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetTranslatableOption()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ]
        );

        $this->builder->get('test')->setOption('translatable', true);

        $this->assertTrue($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAdd()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
            ]
        );

        $this->assertTrue($this->builder->get('test')->hasOption('translatable'));
        $this->assertTrue($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddUntranslatable()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'translatable' => false,
            ]
        );

        $this->assertTrue($this->builder->get('test')->hasOption('translatable'));
        $this->assertFalse($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddCompound()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class
        );

        $this->assertTrue($this->builder->get('test')->hasOption('translatable'));
        $this->assertTrue($this->builder->get('test')->getOption('translatable'));

        $this->assertTrue($this->builder->get('test')->get('test2')->hasOption('translatable'));
        $this->assertTrue($this->builder->get('test')->get('test2')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddCompoundUntranslatable()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'translatable' => false,
            ]
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ]
        );

        $this->assertTrue($this->builder->get('test')->hasOption('translatable'));
        $this->assertFalse($this->builder->get('test')->getOption('translatable'));

        $this->assertTrue($this->builder->get('test')->get('test2')->hasOption('translatable'));
        $this->assertFalse($this->builder->get('test')->get('test2')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage Parameter "test2" cannot be translatable, since its parent parameter "test" is not translatable
     */
    public function testAddThrowsInvalidOptionsExceptionOnAddingTranslatableParameterToNonTranslatableCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'translatable' => false,
            ]
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilder::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage Parameter "test2" needs to be translatable, since its parent parameter "test" is translatable
     */
    public function testAddThrowsInvalidOptionsExceptionOnAddingNonTranslatableParameterToTranslatableCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ]
        );
    }
}
