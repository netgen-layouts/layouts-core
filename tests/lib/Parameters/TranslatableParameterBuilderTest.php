<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Parameters\ParameterBuilder;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

#[CoversClass(ParameterBuilder::class)]
final class TranslatableParameterBuilderTest extends TestCase
{
    private ParameterBuilderInterface $builder;

    protected function setUp(): void
    {
        $registry = new ParameterTypeRegistry(
            [
                ParameterType\TextType::getIdentifier() => new ParameterType\TextType(),
                ParameterType\Compound\BooleanType::getIdentifier() => new ParameterType\Compound\BooleanType(),
            ],
        );

        $factory = new ParameterBuilderFactory($registry);

        $this->builder = $factory->createParameterBuilder();
    }

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

        self::assertTrue($this->builder->get('test')->isTranslatable());
    }

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

        self::assertTrue($this->builder->get('test')->isTranslatable());
    }

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

        self::assertFalse($this->builder->get('test')->isTranslatable());
    }

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

        self::assertTrue($this->builder->get('test')->isTranslatable());
        self::assertTrue($this->builder->get('test')->get('test2')->isTranslatable());
    }

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

        self::assertFalse($this->builder->get('test')->isTranslatable());
        self::assertFalse($this->builder->get('test')->get('test2')->isTranslatable());
    }

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
