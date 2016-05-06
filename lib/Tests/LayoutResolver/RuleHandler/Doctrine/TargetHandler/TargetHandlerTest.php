<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

abstract class TargetHandlerTest extends \PHPUnit_Framework_TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected $handler;

    /**
     * Sets up the test.
     */
    protected function setUp()
    {
        $this->prepareDatabase(__DIR__ . '/../_fixtures/schema', __DIR__ . '/../_fixtures');

        $this->handler = new Handler($this->databaseConnection, new Normalizer());
        $this->handler->addTargetHandler($this->getTargetIdentifier(), $this->getTargetHandler());
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * Returns the target identifier under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetIdentifier();

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetHandler();
}
