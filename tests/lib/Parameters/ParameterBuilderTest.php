<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParameterBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactory
     */
    private $factory;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->registry = new ParameterTypeRegistry();
        $this->registry->addParameterType(new ParameterType\TextType());
        $this->registry->addParameterType(new ParameterType\IntegerType());
        $this->registry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->factory = new ParameterBuilderFactory($this->registry);

        $this->builder = $this->factory->createParameterBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getName
     */
    public function testGetName()
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

        $this->assertEquals('test', $this->builder->get('test')->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getType
     */
    public function testGetType()
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

        $this->assertEquals(
            $this->registry->getParameterType('text'),
            $this->builder->get('test')->getType()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getOptions
     */
    public function testGetOptions()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ]
        );

        $this->assertEquals(
            ['reverse' => true],
            $this->builder->get('test')->getOptions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getOption
     */
    public function testGetOption()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ]
        );

        $this->assertTrue($this->builder->get('test')->getOption('reverse'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getOption
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     * @expectedExceptionMessage Option "unknown" does not exist in the builder for "test" parameter.
     */
    public function testGetOptionThrowsParameterBuilderException()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ]
        );

        $this->assertTrue($this->builder->get('test')->getOption('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::hasOption
     */
    public function testHasOption()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ]
        );

        $this->assertTrue($this->builder->get('test')->hasOption('reverse'));
        $this->assertFalse($this->builder->get('test')->hasOption('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetOption()
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'min' => 5,
                'max' => 100,
            ]
        );

        $this->builder->get('test')->setOption('min', 42);

        $this->assertEquals(42, $this->builder->get('test')->getOption('min'));
        $this->assertEquals(100, $this->builder->get('test')->getOption('max'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetRequiredOption()
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'required' => true,
            ]
        );

        $this->builder->get('test')->setOption('required', false);

        $this->assertFalse($this->builder->get('test')->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetDefaultValueOption()
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'default_value' => 'test',
            ]
        );

        $this->builder->get('test')->setOption('default_value', 'test2');

        $this->assertEquals('test2', $this->builder->get('test')->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetLabelOption()
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'label' => 'test',
            ]
        );

        $this->builder->get('test')->setOption('label', 'test2');

        $this->assertEquals('test2', $this->builder->get('test')->getLabel());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     */
    public function testSetGroupsOption()
    {
        $this->builder->add(
            'test',
            ParameterType\IntegerType::class,
            [
                'groups' => ['test'],
            ]
        );

        $this->builder->get('test')->setOption('groups', ['test2']);

        $this->assertEquals(['test2'], $this->builder->get('test')->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setOption
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the options is not possible after parameters have been built.
     */
    public function testSetOptionAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setOption('required', true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::isRequired
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setRequired
     */
    public function testGetSetRequired()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setRequired(true);

        $this->assertTrue($this->builder->get('test')->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setRequired
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the required flag is not possible after parameters have been built.
     */
    public function testSetRequiredAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setRequired(true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setDefaultValue
     */
    public function testGetSetDefaultValue()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setDefaultValue(42);

        $this->assertEquals(42, $this->builder->get('test')->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setDefaultValue
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the default value is not possible after parameters have been built.
     */
    public function testSetDefaultValueAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setDefaultValue('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setLabel
     */
    public function testGetSetLabel()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setLabel('Custom label');

        $this->assertEquals('Custom label', $this->builder->get('test')->getLabel());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setLabel
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the label is not possible after parameters have been built.
     */
    public function testSetLabelAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setLabel('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setGroups
     */
    public function testGetSetGroups()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setGroups(['group']);

        $this->assertEquals(['group'], $this->builder->get('test')->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     */
    public function testGetGroupsWithoutParentBuilder()
    {
        $this->assertEquals([], $this->builder->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     */
    public function testGetSetConstraints()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setConstraints([new NotBlank(), function () {}]);

        $this->assertEquals([new NotBlank(), function () {}], $this->builder->get('test')->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getConstraints
     */
    public function testGetConstraintsWithoutParentBuilder()
    {
        $this->assertEquals([], $this->builder->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     * @expectedExceptionMessage Parameter constraints need to be either a Symfony constraint or a closure.
     */
    public function testSetConstraintsWithInvalidConstraints()
    {
        $this->builder->setConstraints([new stdClass()]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setConstraints
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the constraints is not possible after parameters have been built.
     */
    public function testSetConstraintsAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setConstraints([]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     */
    public function testGetGroupsWithCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'groups' => ['group'],
                'reverse' => true,
            ]
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ]
        );

        $this->assertEquals(['group'], $this->builder->get('test')->get('test2')->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setGroups
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Setting the groups is not possible after parameters have been built.
     */
    public function testSetGroupsAfterBuildingParameters()
    {
        $this->builder->buildParameterDefinitions();
        $this->builder->setGroups([]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::count
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
                'constraints' => [new NotBlank()],
            ]
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ]
        );

        $this->assertCount(2, $this->builder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Parameters cannot be added after they have been built.
     */
    public function testAddAfterBuildingParameters()
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

        $this->builder->buildParameterDefinitions();

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     * @expectedExceptionMessage Parameters cannot be added to non-compound parameters.
     */
    public function testAddThrowsParameterBuilderExceptionOnAddingParameterToNonCompoundParameter()
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

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     * @expectedExceptionMessage Compound parameters cannot be added to compound parameters.
     */
    public function testAddThrowsParameterBuilderExceptionOnAddingCompoundParameterToCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\Compound\BooleanType::class
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::has
     */
    public function testHas()
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

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => ['group 2'],
            ]
        );

        $this->assertTrue($this->builder->has('test'));
        $this->assertTrue($this->builder->has('test2'));

        $this->assertFalse($this->builder->has('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::get
     */
    public function testGet()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ]
        );

        $compoundBuilder = $this->builder->get('test');
        $this->assertInstanceOf(ParameterBuilderInterface::class, $compoundBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::get
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     * @expectedExceptionMessage Parameter with "unknown" name does not exist in the builder.
     */
    public function testGetThrowsParameterBuilderExceptionWithNonExistingParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ]
        );

        $this->builder->get('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::get
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Accessing parameter builders is not possible after parameters have been built.
     */
    public function testGetAfterBuildingParameters()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => true,
                'default_value' => true,
                'groups' => ['group'],
            ]
        );

        $this->builder->buildParameterDefinitions();

        $this->builder->get('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::all
     */
    public function testAll()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => ['group'],
            ]
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ]
        );

        $parameterBuilders = $this->builder->all();
        $this->assertInternalType('array', $parameterBuilders);

        $this->assertCount(2, $parameterBuilders);
        $this->assertArrayHasKey('test', $parameterBuilders);
        $this->assertArrayHasKey('test2', $parameterBuilders);

        $this->assertInstanceOf(ParameterBuilderInterface::class, $parameterBuilders['test']);
        $this->assertInstanceOf(ParameterBuilderInterface::class, $parameterBuilders['test2']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::all
     */
    public function testAllWithGroupFilter()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => ['group'],
            ]
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            [
                'groups' => ['group2'],
            ]
        );

        $parameterBuilders = $this->builder->all('group');
        $this->assertInternalType('array', $parameterBuilders);

        $this->assertCount(1, $parameterBuilders);
        $this->assertArrayHasKey('test', $parameterBuilders);

        $this->assertInstanceOf(ParameterBuilderInterface::class, $parameterBuilders['test']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::all
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Accessing parameter builders is not possible after parameters have been built.
     */
    public function testAllAfterBuildingParameters()
    {
        $this->builder->add('test', ParameterType\TextType::class);

        $this->builder->buildParameterDefinitions();

        $this->builder->all();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::remove
     */
    public function testRemove()
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

        $this->builder->remove('test');

        $this->assertCount(0, $this->builder);
        $this->assertFalse($this->builder->has('test'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::remove
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
     * @expectedExceptionMessage Removing parameters is not possible after parameters have been built.
     */
    public function testRemoveAfterBuildingParameters()
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

        $this->builder->buildParameterDefinitions();

        $this->builder->remove('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     */
    public function testBuildParameterDefinitions()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'label' => null,
                'groups' => ['group'],
                'constraints' => [new NotBlank(), function () {}],
            ]
        );

        $this->builder->add(
            'compound',
            ParameterType\Compound\BooleanType::class,
            [
                'required' => false,
                'default_value' => true,
                'label' => false,
                'groups' => ['group 2'],
            ]
        );

        $this->builder->get('compound')->add(
            'test2',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value 2',
                'label' => 'Custom label',
                'groups' => ['group'],
            ]
        );

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        $this->assertEquals(
            $parameterDefinitions,
            [
                'test' => new ParameterDefinition(
                    [
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => [],
                        'isRequired' => true,
                        'defaultValue' => 'test value',
                        'label' => null,
                        'groups' => ['group'],
                        'constraints' => [new NotBlank(), function () {}],
                    ]
                ),
                'compound' => new CompoundParameterDefinition(
                    [
                        'name' => 'compound',
                        'type' => $this->registry->getParameterType('compound_boolean'),
                        'options' => ['reverse' => false],
                        'isRequired' => false,
                        'defaultValue' => true,
                        'label' => false,
                        'groups' => ['group 2'],
                        'constraints' => [],
                        'parameterDefinitions' => [
                            'test2' => new ParameterDefinition(
                                [
                                    'name' => 'test2',
                                    'type' => $this->registry->getParameterType('text'),
                                    'options' => [],
                                    'isRequired' => true,
                                    'defaultValue' => 'test value 2',
                                    'label' => 'Custom label',
                                    'groups' => ['group 2'],
                                    'constraints' => [],
                                ]
                            ),
                        ],
                    ]
                ),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     */
    public function testBuildParameterDefinitionsAfterBuildingParameters()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => true,
                'default_value' => 'test value',
                'label' => null,
                'groups' => ['group'],
                'constraints' => [new NotBlank()],
            ]
        );

        $this->builder->buildParameterDefinitions();

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        $this->assertEquals(
            $parameterDefinitions,
            [
                'test' => new ParameterDefinition(
                    [
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => [],
                        'isRequired' => true,
                        'defaultValue' => 'test value',
                        'label' => null,
                        'groups' => ['group'],
                        'constraints' => [new NotBlank()],
                    ]
                ),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     */
    public function testBuildParameterDefinitionsWithDefaultOptions()
    {
        $this->builder->add('test', ParameterType\TextType::class);

        $parameterDefinitions = $this->builder->buildParameterDefinitions();

        $this->assertEquals(
            $parameterDefinitions,
            [
                'test' => new ParameterDefinition(
                    [
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => [],
                        'isRequired' => false,
                        'defaultValue' => null,
                        'label' => null,
                        'groups' => [],
                        'constraints' => [],
                    ]
                ),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "required" with value "true" is expected to be of type "bool", but is of type "string".
     */
    public function testBuildParameterDefinitionsWithInvalidRequiredOption()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'required' => 'true',
            ]
        );

        $this->builder->buildParameterDefinitions();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "groups" with value "group" is expected to be of type "array", but is of type "string".
     */
    public function testBuildParameterDefinitionsWithInvalidGroupsOption()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'groups' => 'group',
            ]
        );

        $this->builder->buildParameterDefinitions();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::validateConstraints
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "label" with value true is invalid.
     */
    public function testBuildParameterDefinitionsWithInvalidLabel()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            [
                'label' => true,
            ]
        );

        $this->builder->buildParameterDefinitions();
    }
}
