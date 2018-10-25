<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use PDO;
use PHPUnit\Framework\TestCase;

final class PositionHelperTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    private $positionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->collectionHandler = $this->createCollectionHandler();
        $this->positionHelper = new PositionHelper($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     */
    public function testCreatePosition(): void
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions(), 1);

        self::assertSame(1, $newPosition);

        self::assertSame(
            [0, 2, 3],
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     */
    public function testCreatePositionAtLastPlace(): void
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions());

        self::assertSame(3, $newPosition);

        self::assertSame(
            [0, 1, 2],
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     */
    public function testCreatePositionThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position is out of range.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     */
    public function testCreatePositionThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position cannot be negative.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::createPosition
     */
    public function testCreatePositionThrowsBadStateExceptionOnInvalidEndPosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('When creating a position, end position needs to be greater or equal than start position.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), 1, 0);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::decrementPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     */
    public function testMoveToPosition(): void
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 0, 2);

        self::assertSame(
            [0, 0, 1],
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::incrementPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     */
    public function testMoveToLowerPosition(): void
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 2, 0);

        self::assertSame(
            [1, 2, 2],
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     */
    public function testMoveToPositionBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position is out of range.');

        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::moveToPosition
     */
    public function testMoveToPositionBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position cannot be negative.');

        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::decrementPositions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::removePosition
     */
    public function testRemovePosition(): void
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
            ->setParameter('status', Value::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        $this->positionHelper->removePosition($this->getPositionHelperConditions(), 1);

        self::assertSame(
            [0, 1],
            $this->getPositionData()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::applyConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper::getNextPosition
     */
    public function testGetNextPosition(): void
    {
        self::assertSame(3, $this->positionHelper->getNextPosition($this->getPositionHelperConditions()));
    }

    /**
     * Builds the condition array that will be used with position helper.
     */
    private function getPositionHelperConditions(): array
    {
        return [
            'table' => 'ngbm_collection_item',
            'column' => 'position',
            'conditions' => [
                'collection_id' => 1,
                'status' => Value::STATUS_DRAFT,
            ],
        ];
    }

    /**
     * Returns the position data from the table under test.
     */
    private function getPositionData(): array
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
            ->setParameter('status', Value::STATUS_DRAFT, Type::INTEGER)
            ->orderBy('position', 'ASC');

        $result = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($result, 'position'));
    }
}
