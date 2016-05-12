<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase;

class PositionHelperTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        $this->collectionHandler = $this->createPersistenceHandler()->getCollectionHandler();
        $this->positionHelper = new PositionHelper($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     */
    public function testCreatePosition()
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions(), 1);

        self::assertEquals(1, $newPosition);

        self::assertEquals(
            array(0, 2, 3),
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     */
    public function testCreatePositionAtLastPlace()
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions());

        self::assertEquals(3, $newPosition);

        self::assertEquals(
            array(0, 1, 2),
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreatePositionThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->positionHelper->createPosition($this->getPositionHelperConditions(), 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreatePositionThrowsBadStateExceptionOnNegativePosition()
    {
        $this->positionHelper->createPosition($this->getPositionHelperConditions(), -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::decrementPositions
     */
    public function testMoveToPosition()
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 0, 2);

        self::assertEquals(
            array(0, 0, 1),
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     */
    public function testMoveToLowerPosition()
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 2, 0);

        self::assertEquals(
            array(1, 2, 2),
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveToPositionBadStateExceptionOnTooLargePosition()
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveToPositionBadStateExceptionOnNegativePosition()
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::removePosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::decrementPositions
     */
    public function testRemovePosition()
    {
        $query = $this->databaseConnection->createQueryBuilder();

        $query->delete('ngbm_collection_item')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('id', 2, Type::INTEGER)
            ->setParameter('status', Collection::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        $this->positionHelper->removePosition($this->getPositionHelperConditions(), 1);

        self::assertEquals(
            array(0, 1),
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::getNextPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::applyConditions
     */
    public function testGetNextPosition()
    {
        self::assertEquals(3, $this->positionHelper->getNextPosition($this->getPositionHelperConditions()));
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @return array
     */
    protected function getPositionHelperConditions()
    {
        return array(
            'table' => 'ngbm_collection_item',
            'column' => 'position',
            'conditions' => array(
                'collection_id' => 1,
                'status' => Collection::STATUS_DRAFT,
            ),
        );
    }

    /**
     * Returns the position data from the table under test.
     *
     * @return array
     */
    protected function getPositionData()
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $query->select('position')
            ->from('ngbm_collection_item')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('collection_id', ':collection_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('collection_id', 1, Type::INTEGER)
            ->setParameter('status', Collection::STATUS_DRAFT, Type::INTEGER)
            ->orderBy('position', 'ASC');

        return array_map(
            function ($dataRow) {
                return $dataRow['position'];
            },
            $query->execute()->fetchAll()
        );
    }
}
