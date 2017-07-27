<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Collection\QueryTranslation;
use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Exception\Core\TranslationException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getTranslations
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getTranslation
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasTranslation
     */
    public function testSetDefaultProperties()
    {
        $query = new Query();

        $this->assertNull($query->getId());
        $this->assertNull($query->getStatus());
        $this->assertNull($query->getCollectionId());
        $this->assertNull($query->getQueryType());
        $this->assertNull($query->isPublished());
        $this->assertNull($query->isTranslatable());
        $this->assertNull($query->getMainLocale());
        $this->assertNull($query->isAlwaysAvailable());
        $this->assertEquals(array(), $query->getAvailableLocales());

        $this->assertEquals(array(), $query->getTranslations());
        $this->assertFalse($query->hasTranslation('en'));

        try {
            $query->getTranslation('en');
        } catch (TranslationException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getTranslations
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getTranslation
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasTranslation
     */
    public function testSetProperties()
    {
        $queryTranslation = new QueryTranslation(
            array(
                'locale' => 'en',
                'parameters' => array(
                    'param' => 'value',
                ),
            )
        );

        $query = new Query(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 30,
                'queryType' => new QueryType('query_type'),
                'published' => true,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => array('en'),
                'translations' => array(
                    'en' => $queryTranslation,
                ),
            )
        );

        $this->assertEquals(42, $query->getId());
        $this->assertTrue($query->isPublished());
        $this->assertEquals(30, $query->getCollectionId());
        $this->assertEquals(new QueryType('query_type'), $query->getQueryType());
        $this->assertEquals(array('param' => 'value'), $query->getParameters());
        $this->assertEquals('value', $query->getParameter('param'));
        $this->assertFalse($query->hasParameter('test'));
        $this->assertTrue($query->hasParameter('param'));
        $this->assertEquals($queryTranslation, $query->getTranslation('en'));
        $this->assertFalse($query->hasTranslation('hr'));
        $this->assertTrue($query->hasTranslation('en'));
        $this->assertEquals(Value::STATUS_PUBLISHED, $query->getStatus());
        $this->assertTrue($query->isTranslatable());
        $this->assertEquals('en', $query->getMainLocale());
        $this->assertEquals(true, $query->isAlwaysAvailable());
        $this->assertEquals(array('en'), $query->getAvailableLocales());

        $this->assertEquals(
            array(
                'param' => 'value',
            ),
            $query->getParameters()
        );

        try {
            $query->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals(
            array(
                'en' => $queryTranslation,
            ),
            $query->getTranslations()
        );

        try {
            $query->getTranslation('hr');
        } catch (TranslationException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getInternalLimit
     */
    public function testGetInternalLimit()
    {
        $query = new Query(
            array(
                'queryType' => new QueryType('query_type', array(), 3),
            )
        );

        $this->assertEquals(3, $query->getInternalLimit());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isContextual
     */
    public function testIsContextual()
    {
        $query = new Query(
            array(
                'queryType' => new QueryType('query_type'),
            )
        );

        $this->assertFalse($query->isContextual());
    }
}
