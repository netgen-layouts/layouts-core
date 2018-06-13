<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(Value::class, new Query());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     */
    public function testDefaultProperties()
    {
        $query = new Query();

        $this->assertEquals([], $query->getParameters());
        $this->assertEquals([], $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isTranslatable
     */
    public function testSetProperties()
    {
        $query = new Query(
            [
                'id' => 42,
                'collectionId' => 30,
                'queryType' => new QueryType('query_type'),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [
                    'param' => 'value',
                ],
            ]
        );

        $this->assertEquals(42, $query->getId());
        $this->assertEquals(30, $query->getCollectionId());
        $this->assertEquals(new QueryType('query_type'), $query->getQueryType());
        $this->assertEquals(['param' => 'value'], $query->getParameters());
        $this->assertEquals('value', $query->getParameter('param'));
        $this->assertFalse($query->hasParameter('test'));
        $this->assertTrue($query->hasParameter('param'));
        $this->assertTrue($query->isTranslatable());
        $this->assertEquals('en', $query->getMainLocale());
        $this->assertTrue($query->isAlwaysAvailable());
        $this->assertEquals(['en'], $query->getAvailableLocales());
        $this->assertEquals('en', $query->getLocale());

        $this->assertEquals(
            [
                'param' => 'value',
            ],
            $query->getParameters()
        );

        try {
            $query->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isContextual
     */
    public function testIsContextual()
    {
        $query = new Query(
            [
                'queryType' => new QueryType('query_type'),
            ]
        );

        $this->assertFalse($query->isContextual());
    }
}
