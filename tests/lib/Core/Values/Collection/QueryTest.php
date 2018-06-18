<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Query());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     */
    public function testDefaultProperties(): void
    {
        $query = new Query();

        $this->assertSame([], $query->getParameters());
        $this->assertSame([], $query->getAvailableLocales());
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
    public function testSetProperties(): void
    {
        $queryType = new QueryType('query_type');
        $parameter = new Parameter(['value' => 'value']);

        $query = new Query(
            [
                'id' => 42,
                'collectionId' => 30,
                'queryType' => $queryType,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [
                    'param' => $parameter,
                ],
            ]
        );

        $this->assertSame(42, $query->getId());
        $this->assertSame(30, $query->getCollectionId());
        $this->assertSame($queryType, $query->getQueryType());
        $this->assertSame(['param' => $parameter], $query->getParameters());
        $this->assertSame($parameter, $query->getParameter('param'));
        $this->assertFalse($query->hasParameter('test'));
        $this->assertTrue($query->hasParameter('param'));
        $this->assertTrue($query->isTranslatable());
        $this->assertSame('en', $query->getMainLocale());
        $this->assertTrue($query->isAlwaysAvailable());
        $this->assertSame(['en'], $query->getAvailableLocales());
        $this->assertSame('en', $query->getLocale());

        $this->assertSame(
            [
                'param' => $parameter,
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
    public function testIsContextual(): void
    {
        $query = new Query(
            [
                'queryType' => new QueryType('query_type'),
            ]
        );

        $this->assertFalse($query->isContextual());
    }
}
