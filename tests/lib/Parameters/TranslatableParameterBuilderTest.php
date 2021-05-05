<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class TranslatableParameterBuilderTest extends TestCase
{
    private ParameterTypeRegistry $registry;

    private TranslatableParameterBuilderFactory $factory;

    private ParameterBuilderInterface $builder;

    protected function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry(
            [
                new ParameterType\TextType(),
                new ParameterType\Compound\BooleanType(),
            ],
        );

        $this->factory = new TranslatableParameterBuilderFactory($this->registry);

        $this->builder = $this->factory->createParameterBuilder();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterBuilder::setOption
     */
    public function testSetTranslatableOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ],
        );

        $this->builder->get('test')->setOption('translatable', true);

        self::assertTrue($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAdd(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
            ],
        );

        self::assertTrue($this->builder->get('test')->hasOption('translatable'));
        self::assertTrue($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddUntranslatable(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'translatable' => false,
            ],
        );

        self::assertTrue($this->builder->get('test')->hasOption('translatable'));
        self::assertFalse($this->builder->get('test')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddCompound(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
        );

        self::assertTrue($this->builder->get('test')->hasOption('translatable'));
        self::assertTrue($this->builder->get('test')->getOption('translatable'));

        self::assertTrue($this->builder->get('test')->get('test2')->hasOption('translatable'));
        self::assertTrue($this->builder->get('test')->get('test2')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddCompoundUntranslatable(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'translatable' => false,
            ],
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ],
        );

        self::assertTrue($this->builder->get('test')->hasOption('translatable'));
        self::assertFalse($this->builder->get('test')->getOption('translatable'));

        self::assertTrue($this->builder->get('test')->get('test2')->hasOption('translatable'));
        self::assertFalse($this->builder->get('test')->get('test2')->getOption('translatable'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddThrowsInvalidOptionsExceptionOnAddingTranslatableParameterToNonTranslatableCompoundParameter(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Parameter "test2" cannot be translatable, since its parent parameter "test" is not translatable');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'translatable' => false,
            ],
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::add
     * @covers \Netgen\Layouts\Parameters\TranslatableParameterBuilder::configureOptions
     */
    public function testAddThrowsInvalidOptionsExceptionOnAddingNonTranslatableParameterToTranslatableCompoundParameter(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Parameter "test2" needs to be translatable, since its parent parameter "test" is translatable');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'translatable' => false,
            ],
        );
    }
}
