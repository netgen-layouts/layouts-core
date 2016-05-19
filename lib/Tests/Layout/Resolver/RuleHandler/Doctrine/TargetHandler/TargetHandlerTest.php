<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Handler;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;

abstract class TargetHandlerTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected $handler;

    /**
     * Sets up the test.
     */
    protected function setUp()
    {
        $this->prepareDatabase(
            __DIR__ . '/../../../../../_fixtures/schema',
            __DIR__ . '/../../../../../_fixtures'
        );

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
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetIdentifier();

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetHandler();
}
