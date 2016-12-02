<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Block;

use Netgen\BlockManager\Tests\Core\Service\Block\HtmlSnippetTest as BaseHtmlSnippetTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\HtmlSnippetHandler::buildParameters
 */
class HtmlSnippetTest extends BaseHtmlSnippetTest
{
    use TestCaseTrait;

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }

    public function tearDown()
    {
        $this->closeDatabase();
    }
}
