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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetThrowsInvalidArgumentExceptionWithNonCompoundParameter()
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

        $this->builder->get('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::add
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilder::get
     * @expectedException \Netgen\BlockManager\Exception\BadMethodCallException
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
     */
    public function testBuildParameters()
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
            'compound',
            ParameterType\Compound\BooleanType::class,
            array(
                'required' => false,
                'default_value' => true,
                'groups' => array('group 2'),
            )
        );

        $this->builder->get('compound')->add(
            'test2',
            ParameterType\TextType::class,
            array(
                'required' => true,
                'default_value' => 'test value 2',
                'groups' => array('group'),
            )
        );

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    'test',
                    $this->registry->getParameterType('text'),
                    array(
                        'required' => true,
                        'default_value' => 'test value',
                        'groups' => array('group'),
                    )
                ),
                'compound' => new CompoundParameter(
                    'compound',
                    $this->registry->getParameterType('compound_boolean'),
                    array(
                        'required' => false,
                        'default_value' => true,
                        'groups' => array('group 2'),
                        'reverse' => false,
                    ),
                    array(
                        'test2' => new Parameter(
                            'test2',
                            $this->registry->getParameterType('text'),
                            array(
                                'required' => true,
                                'default_value' => 'test value 2',
                                'groups' => array('group 2'),
                            )
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
     */
    public function testBuildParametersAfterBuildingParameters()
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

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    'test',
                    $this->registry->getParameterType('text'),
                    array(
                        'required' => true,
                        'default_value' => 'test value',
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
     */
    public function testBuildParametersWithDefaultOptions()
    {
        $this->builder->add('test', ParameterType\TextType::class);

        $parameters = $this->builder->buildParameters();

        $this->assertEquals(
            $parameters,
            array(
                'test' => new Parameter(
                    'test',
                    $this->registry->getParameterType('text'),
                    array(
                        'required' => false,
                        'default_value' => null,
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
}
