<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;
use Netgen\BlockManager\Tests\DoctrineDatabaseTrait;

abstract class TargetHandlerTest extends \PHPUnit_Framework_TestCase
{
    use DoctrineDatabaseTrait;

    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $this->prepareDatabase(__DIR__ . '/../_fixtures/schema', __DIR__ . '/../_fixtures');
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * Creates the handler under test.
     *
     * @param string $targetIdentifier
     * @param \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler $targetHandler
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface
     */
    protected function createHandler($targetIdentifier, TargetHandler $targetHandler)
    {
        $handler = new Handler($this->databaseConnection, new Normalizer());
        $handler->addTargetHandler($targetIdentifier, $targetHandler);

        return $handler;
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetHandler();
}
