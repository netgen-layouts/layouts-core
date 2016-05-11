<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler
     */
    protected $handler;

    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $this->prepareDatabase(
            __DIR__ . '/../../../_fixtures/schema',
            __DIR__ . '/../../../_fixtures'
        );

        $this->handler = new Handler($this->databaseConnection, new Normalizer());
        $this->handler->addTargetHandler('route', new Route());
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::addTargetHandler
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 1,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules('route', array('my_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::addTargetHandler
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithCondition()
    {
        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_param' => array('1', '2')),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules('route', array('my_second_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithMultipleConditions()
    {
        $expected = array(
            array(
                'layout_id' => 3,
                'conditions' => array(
                    array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_param' => array('3', '4')),
                    ),
                    array(
                        'identifier' => 'route_parameter',
                        'parameters' => array('some_other_param' => array('5', '6')),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules('route', array('my_fourth_cool_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     */
    public function testLoadRulesWithNoRules()
    {
        self::assertEquals(array(), $this->handler->loadRules('route', array('some_non_existing_route')));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler::loadRules
     * @expectedException \InvalidArgumentException
     */
    public function testLoadRulesWithNonExistingTargetHandler()
    {
        $this->handler->loadRules('non_existing_target', array(1, 2, 3));
    }
}
