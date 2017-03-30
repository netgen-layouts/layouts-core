<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\CompoundParameter;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

class ParameterBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->registry = new ParameterTypeRegistry();
        $this->registry->addParameterType(new ParameterType\TextType());
        $this->registry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->builder = new ParameterBuilder($this->registry);
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
                'reverse' => true,
            )
        );

        $this->assertEquals(
            array('reverse' => true),
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
                'reverse' => true,
            )
        );

        $this->assertTrue($this->builder->get('test')->getOption('reverse'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getOption
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Option "unknown" does not exist in builder for "test" parameter
     */
    public function testGetOptionThrowsInvalidArgumentException()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
                'reverse' => true,
            )
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
                'reverse' => true,
            )
        );

        $this->assertTrue($this->builder->get('test')->hasOption('reverse'));
        $this->assertFalse($this->builder->get('test')->hasOption('unknown'));
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
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::setGroups
     */
    public function testGetSetGroups()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class
        );

        $this->builder->get('test')->setGroups(array('group'));

        $this->assertEquals(array('group'), $this->builder->get('test')->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     */
    public function testGetGroupsWithoutParentBuilder()
    {
        $this->assertEquals(array(), $this->builder->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::getGroups
     */
    public function testGetGroupsWithCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
                'reverse' => true,
            )
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'groups' => array('group2'),
            )
        );

        $this->assertEquals(array('group'), $this->builder->get('test')->get('test2')->getGroups());
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => array('group 2'),
            )
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
        );

        $this->builder->buildParameters();

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => array('group 2'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameters cannot be added to non-compound parameters.
     */
    public function testAddThrowsInvalidArgumentExceptionOnAddingParameterToNonCompoundParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
        );

        $this->builder->get('test')->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => array('group 2'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Compound parameters cannot be added to compound parameters.
     */
    public function testAddThrowsInvalidArgumentExceptionOnAddingCompoundParameterToCompoundParameter()
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
        );

        $this->builder->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => false,
                'default_value' => 'test value 2',
                'groups' => array('group 2'),
            )
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
            array(
                'required' => true,
                'default_value' => true,
                'groups' => array('group'),
            )
        );

        $compoundBuilder = $this->builder->get('test');
        $this->assertInstanceOf(ParameterBuilderInterface::class, $compoundBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::get
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameter with "unknown" name does not exist in the builder.
     */
    public function testGetThrowsInvalidArgumentExceptionWithNonExistingParameter()
    {
        $this->builder->add(
            'test',
            ParameterType\Compound\BooleanType::class,
            array(
                'required' => true,
                'default_value' => true,
                'groups' => array('group'),
            )
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
            array(
                'required' => true,
                'default_value' => true,
                'groups' => array('group'),
            )
        );

        $this->builder->buildParameters();

        $this->builder->get('test');
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
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
            array(
                'required' => true,
                'default_value' => 'test value',
                'groups' => array('group'),
            )
        );

        $this->builder->buildParameters();

        $this->builder->remove('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     */
    public function testBuildParameters()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'required' => true,
                'default_value' => 'test value',
                'label' => null,
                'groups' => array('group'),
            )
        );

        $this->builder->add(
            'compound',
            ParameterType\Compound\BooleanType::class,
            array(
                'required' => false,
                'default_value' => true,
                'label' => false,
                'groups' => array('group 2'),
            )
        );

        $this->builder->get('compound')->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => true,
                'default_value' => 'test value 2',
                'label' => 'Custom label',
                'groups' => array('group'),
            )
        );

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    array(
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => array(),
                        'isRequired' => true,
                        'defaultValue' => 'test value',
                        'label' => null,
                        'groups' => array('group'),
                    )
                ),
                'compound' => new CompoundParameter(
                    array(
                        'name' => 'compound',
                        'type' => $this->registry->getParameterType('compound_boolean'),
                        'options' => array('reverse' => false),
                        'isRequired' => false,
                        'defaultValue' => true,
                        'label' => false,
                        'groups' => array('group 2'),
                        'parameters' => array(
                            'test2' => new Parameter(
                                array(
                                    'name' => 'test2',
                                    'type' => $this->registry->getParameterType('text'),
                                    'options' => array(),
                                    'isRequired' => true,
                                    'defaultValue' => 'test value 2',
                                    'label' => 'Custom label',
                                    'groups' => array('group 2'),
                                )
                            ),
                        ),
                    )
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     */
    public function testBuildParametersAfterBuildingParameters()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'required' => true,
                'default_value' => 'test value',
                'label' => null,
                'groups' => array('group'),
            )
        );

        $this->builder->buildParameters();

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    array(
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => array(),
                        'isRequired' => true,
                        'defaultValue' => 'test value',
                        'label' => null,
                        'groups' => array('group'),
                    )
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     */
    public function testBuildParametersWithDefaultOptions()
    {
        $this->builder->add('test', ParameterType\TextType::class);

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    array(
                        'name' => 'test',
                        'type' => $this->registry->getParameterType('text'),
                        'options' => array(),
                        'isRequired' => false,
                        'defaultValue' => null,
                        'label' => null,
                        'groups' => array(),
                    )
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testBuildParametersWithInvalidRequiredOption()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'required' => 'true',
            )
        );

        $this->builder->buildParameters();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testBuildParametersWithInvalidGroupsOption()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'groups' => 'group',
            )
        );

        $this->builder->buildParameters();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::buildParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::resolveOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testBuildParametersWithInvalidLabel()
    {
        $this->builder->add(
            'test',
            ParameterType\TextType::class,
            array(
                'label' => true,
            )
        );

        $this->builder->buildParameters();
    }
}
