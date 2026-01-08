<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\BadMethodCallException;
use Netgen\Layouts\Exception\Parameters\ParameterBuilderException;
use Netgen\Layouts\Parameters\ParameterBuilder;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(ParameterBuilder::class)]
final class ParameterBuilderTest extends TestCase
{
    use ExportObjectTrait;

    private ParameterTypeRegistry $registry;

    private ParameterBuilderInterface $builder;

    protected function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry(
            [
                new ParameterType\TextType(),
                new ParameterType\IntegerType(),
                new ParameterType\Compound\BooleanType(),
            ],
        );

        $factory = new ParameterBuilderFactory($this->registry);

        $this->builder = $factory->createParameterBuilder();
    }

    public function testGetName(): void
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

        self::assertSame('test', $this->builder->get('test')->getName());
    }

    public function testGetType(): void
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

        self::assertSame(
            $this->registry->getParameterType('text'),
            $this->builder->get('test')->getType(),
        );
    }

    public function testGetOptions(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ],
        );

        self::assertSame(
            ['reverse' => true],
            $this->builder->get('test')->getOptions(),
        );
    }

    public function testGetOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ],
        );

        self::assertTrue($this->builder->get('test')->getOption('reverse'));
    }

    public function testGetOptionThrowsParameterBuilderException(): void
    {
        $this->expectException(ParameterBuilderException::class);
        $this->expectExceptionMessage('Option "unknown" does not exist in the builder for "test" parameter.');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ],
        );

        self::assertTrue($this->builder->get('test')->getOption('unknown'));
    }

    public function testHasOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ],
        );

        self::assertTrue($this->builder->get('test')->hasOption('reverse'));
        self::assertFalse($this->builder->get('test')->hasOption('unknown'));
    }

    public function testSetOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'min' => 5,
                'max' => 100,
            ],
        );

        $this->builder->get('test')->setOption('min', 42);

        self::assertSame(42, $this->builder->get('test')->getOption('min'));
        self::assertSame(100, $this->builder->get('test')->getOption('max'));
    }

    public function testSetRequiredOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'required' => true,
            ],
        );

        $this->builder->get('test')->setOption('required', false);

        self::assertFalse($this->builder->get('test')->isRequired());
    }

    public function testSetDefaultValueOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'default_value' => 'test',
            ],
        );

        $this->builder->get('test')->setOption('default_value', 'test2');

        self::assertSame('test2', $this->builder->get('test')->getDefaultValue());
    }

    public function testSetLabelOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'label' => 'test',
            ],
        );

        $this->builder->get('test')->setOption('label', 'test2');

        self::assertSame('test2', $this->builder->get('test')->getLabel());
    }

    public function testSetGroupsOption(): void
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'groups' => ['test'],
            ],
        );

        $this->builder->get('test')->setOption('groups', ['test2']);

        self::assertSame(['test2'], $this->builder->get('test')->getGroups());
    }

    public function testSetOptionAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the options is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setOption('required', true);
    }

    public function testGetSetRequired(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
        );

        $this->builder->get('test')->setRequired(true);

        self::assertTrue($this->builder->get('test')->isRequired());
    }

    public function testSetRequiredAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the required flag is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setRequired(true);
    }

    public function testGetSetDefaultValue(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
        );

        $this->builder->get('test')->setDefaultValue(42);

        self::assertSame(42, $this->builder->get('test')->getDefaultValue());
    }

    public function testSetDefaultValueAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the default value is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setDefaultValue('test');
    }

    public function testGetSetLabel(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
        );

        $this->builder->get('test')->setLabel('Custom label');

        self::assertSame('Custom label', $this->builder->get('test')->getLabel());
    }

    public function testSetLabelAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the label is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setLabel('test');
    }

    public function testGetSetGroups(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
        );

        $this->builder->get('test')->setGroups(['group']);

        self::assertSame(['group'], $this->builder->get('test')->getGroups());
    }

    public function testGetGroupsWithoutParentBuilder(): void
    {
        self::assertSame([], $this->builder->getGroups());
    }

    public function testGetSetConstraints(): void
    {
        $constraints = [new NotBlank(), static fn (): NotBlank => new NotBlank()];

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
        );

        $this->builder->get('test')->setConstraints($constraints);

        self::assertSame($constraints, $this->builder->get('test')->getConstraints());
    }

    public function testGetConstraintsWithoutParentBuilder(): void
    {
        self::assertSame([], $this->builder->getConstraints());
    }

    public function testSetConstraintsAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the constraints is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setConstraints([]);
    }

    public function testGetGroupsWithCompoundParameter(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ],
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ],
        );

        self::assertSame(['group'], $this->builder->get('test')->get('test2')->getGroups());
    }

    public function testSetGroupsAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Setting the groups is not possible after parameters have been built.');

        $this->builder->buildParameterDefinitions();
        $this->builder->setGroups([]);
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
                'constraints' => [new NotBlank()],
            ],
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ],
        );

        self::assertCount(2, $this->builder);
    }

    public function testAddAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Parameters cannot be added after they have been built.');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
            ],
        );

        $this->builder->buildParameterDefinitions();

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ],
        );
    }

    public function testAddThrowsParameterBuilderExceptionOnAddingParameterToNonCompoundParameter(): void
    {
        $this->expectException(ParameterBuilderException::class);
        $this->expectExceptionMessage('Parameters cannot be added to non-compound parameters.');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
            ],
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ],
        );
    }

    public function testAddThrowsParameterBuilderExceptionOnAddingCompoundParameterToCompoundParameter(): void
    {
        $this->expectException(ParameterBuilderException::class);
        $this->expectExceptionMessage('Compound parameters cannot be added to compound parameters.');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\Compound\BooleanType::class,
        );
    }

    public function testHas(): void
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

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ],
        );

        self::assertTrue($this->builder->has('test'));
        self::assertTrue($this->builder->has('test2'));

        self::assertFalse($this->builder->has('unknown'));
    }

    public function testGet(): void
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ],
        );

        self::assertTrue($this->builder->has('test'));
        $this->builder->get('test');
    }

    public function testGetThrowsParameterBuilderExceptionWithNonExistingParameter(): void
    {
        $this->expectException(ParameterBuilderException::class);
        $this->expectExceptionMessage('Parameter with "unknown" name does not exist in the builder.');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ],
        );

        $this->builder->get('unknown');
    }

    public function testGetAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Accessing parameter builders is not possible after parameters have been built.');

        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ],
        );

        $this->builder->buildParameterDefinitions();

        $this->builder->get('test');
    }

    public function testAll(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => ['group'],
            ],
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ],
        );

        $parameterBuilders = $this->builder->all();

        self::assertCount(2, $parameterBuilders);
        self::assertArrayHasKey('test', $parameterBuilders);
        self::assertArrayHasKey('test2', $parameterBuilders);

        self::assertContainsOnlyInstancesOf(ParameterBuilderInterface::class, $parameterBuilders);
    }

    public function testAllWithGroupFilter(): void
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => ['group'],
            ],
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ],
        );

        $parameterBuilders = $this->builder->all('group');

        self::assertCount(1, $parameterBuilders);
        self::assertArrayHasKey('test', $parameterBuilders);
        self::assertContainsOnlyInstancesOf(ParameterBuilderInterface::class, $parameterBuilders);
    }

    public function testAllAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Accessing parameter builders is not possible after parameters have been built.');

        $this->builder->add('test', ParameterType\TextType::class);

        $this->builder->buildParameterDefinitions();

        $this->builder->all();
    }

    public function testRemove(): void
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

        $this->builder->remove('test');

        self::assertCount(0, $this->builder);
        self::assertFalse($this->builder->has('test'));
    }

    public function testRemoveAfterBuildingParameters(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Removing parameters is not possible after parameters have been built.');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
            ],
        );

        $this->builder->buildParameterDefinitions();

        $this->builder->remove('test');
    }

    public function testBuildParameterDefinitions(): void
    {
        $constraints = [new NotBlank(), static function (): void {}];

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'readonly' => true,
                'required' => true,
                'default_value' => 'test value',
                'label' => null,
                'groups' => ['group'],
                'constraints' => $constraints,
            ],
        );

        $this->builder->add(
            'compound',
            ParameterType\Compound\BooleanType::class,
            [
                'readonly' => false,
                'required' => false,
                'default_value' => true,
                'label' => false,
                'groups' => ['group 2'],
            ],
        );

        $this->builder->get('compound')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'readonly' => false,
                'required' => true,
                'default_value' => 'test value 2',
                'label' => 'Custom label',
                'groups' => ['group'],
            ],
        );

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        self::assertArrayHasKey('test', $parameterDefinitions);
        self::assertArrayHasKey('compound', $parameterDefinitions);

        self::assertContainsOnlyInstancesOf(ParameterDefinition::class, $parameterDefinitions);
        self::assertFalse($parameterDefinitions['test']->isCompound);

        $compoundDefinition = $parameterDefinitions['compound'];
        self::assertTrue($compoundDefinition->isCompound);

        $innerDefinitions = $compoundDefinition->parameterDefinitions;

        self::assertArrayHasKey('test2', $innerDefinitions);

        self::assertContainsOnlyInstancesOf(ParameterDefinition::class, $innerDefinitions);
        self::assertFalse($innerDefinitions['test2']->isCompound);

        self::assertSame(
            [
                'constraints' => $constraints,
                'defaultValue' => 'test value',
                'groups' => ['group'],
                'isCompound' => false,
                'isReadOnly' => true,
                'isRequired' => true,
                'isTranslatable' => true,
                'label' => null,
                'name' => 'test',
                'options' => [],
                'parameterDefinitions' => [],
                'type' => $this->registry->getParameterType('text'),
            ],
            $this->exportObject($parameterDefinitions['test']),
        );

        self::assertSame(
            [
                'constraints' => [],
                'defaultValue' => true,
                'groups' => ['group 2'],
                'isCompound' => true,
                'isReadOnly' => false,
                'isRequired' => false,
                'isTranslatable' => true,
                'label' => false,
                'name' => 'compound',
                'options' => ['reverse' => false],
                'parameterDefinitions' => $innerDefinitions,
                'type' => $this->registry->getParameterType('compound_boolean'),
            ],
            $this->exportObject($parameterDefinitions['compound']),
        );

        self::assertSame(
            [
                'constraints' => [],
                'defaultValue' => 'test value 2',
                'groups' => ['group 2'],
                'isCompound' => false,
                'isReadOnly' => false,
                'isRequired' => true,
                'isTranslatable' => true,
                'label' => 'Custom label',
                'name' => 'test2',
                'options' => [],
                'parameterDefinitions' => [],
                'type' => $this->registry->getParameterType('text'),
            ],
            $this->exportObject($innerDefinitions['test2']),
        );
    }

    public function testBuildParameterDefinitionsAfterBuildingParameters(): void
    {
        $constraints = [new NotBlank()];

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'readonly' => false,
                'constraints' => $constraints,
                'default_value' => 'test value',
                'groups' => ['group'],
                'label' => null,
                'required' => true,
            ],
        );

        $this->builder->buildParameterDefinitions();

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        self::assertArrayHasKey('test', $parameterDefinitions);
        self::assertContainsOnlyInstancesOf(ParameterDefinition::class, $parameterDefinitions);

        self::assertSame(
            [
                'constraints' => $constraints,
                'defaultValue' => 'test value',
                'groups' => ['group'],
                'isCompound' => false,
                'isReadOnly' => false,
                'isRequired' => true,
                'isTranslatable' => true,
                'label' => null,
                'name' => 'test',
                'options' => [],
                'parameterDefinitions' => [],
                'type' => $this->registry->getParameterType('text'),
            ],
            $this->exportObject($parameterDefinitions['test']),
        );
    }

    public function testBuildParameterDefinitionsWithDefaultOptions(): void
    {
        $this->builder->add('test', ParameterType\TextType::class);

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        self::assertArrayHasKey('test', $parameterDefinitions);
        self::assertContainsOnlyInstancesOf(ParameterDefinition::class, $parameterDefinitions);

        self::assertSame(
            [
                'constraints' => [],
                'defaultValue' => null,
                'groups' => [],
                'isCompound' => false,
                'isReadOnly' => false,
                'isRequired' => false,
                'isTranslatable' => true,
                'label' => null,
                'name' => 'test',
                'options' => [],
                'parameterDefinitions' => [],
                'type' => $this->registry->getParameterType('text'),
            ],
            $this->exportObject($parameterDefinitions['test']),
        );
    }

    public function testBuildParameterDefinitionsWithInvalidRequiredOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "required" with value "true" is expected to be of type "bool", but is of type "string".');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => 'true',
            ],
        );

        $this->builder->buildParameterDefinitions();
    }

    public function testBuildParameterDefinitionsWithInvalidReadOnlyOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "readonly" with value "true" is expected to be of type "bool", but is of type "string".');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'readonly' => 'true',
            ],
        );

        $this->builder->buildParameterDefinitions();
    }

    public function testBuildParameterDefinitionsWithInvalidGroupsOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "groups" with value "group" is expected to be of type "string[]", but is of type "string".');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => 'group',
            ],
        );

        $this->builder->buildParameterDefinitions();
    }

    public function testBuildParameterDefinitionsWithInvalidGroup(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "groups" with value array is expected to be of type "string[]", but one of the elements is of type "int".');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => [42],
            ],
        );

        $this->builder->buildParameterDefinitions();
    }

    public function testBuildParameterDefinitionsWithInvalidLabel(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "label" with value true is invalid.');

        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'label' => true,
            ],
        );

        $this->builder->buildParameterDefinitions();
    }
}
