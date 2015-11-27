<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Handler;
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
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface
     */
    protected function createHandler()
    {
        $handler = new Handler($this->databaseConnection, new Normalizer());
        $handler->addTargetHandler($this->getTargetHandler());

        return $handler;
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    abstract protected function getTargetHandler();
}
