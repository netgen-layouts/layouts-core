<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\Layouts\Persistence\Values\Status;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function array_column;
use function array_map;

#[CoversClass(PositionHelper::class)]
final class PositionHelperTest extends TestCase
{
    use TestCaseTrait;

    private PositionHelper $positionHelper;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->positionHelper = new PositionHelper($this->databaseConnection);
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    public function testCreatePosition(): void
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions(), 1);

        self::assertSame(1, $newPosition);

        self::assertSame(
            [0, 2, 3],
            $this->getPositionData(),
        );
    }

    public function testCreatePositionAtLastPlace(): void
    {
        $newPosition = $this->positionHelper->createPosition($this->getPositionHelperConditions());

        self::assertSame(3, $newPosition);

        self::assertSame(
            [0, 1, 2],
            $this->getPositionData(),
        );
    }

    public function testCreatePositionThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position is out of range.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), 9999);
    }

    public function testCreatePositionThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position cannot be negative.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), -1);
    }

    public function testCreatePositionThrowsBadStateExceptionOnInvalidEndPosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('When creating a position, end position needs to be greater or equal than start position.');

        $this->positionHelper->createPosition($this->getPositionHelperConditions(), 1, 0);
    }

    public function testMoveToPosition(): void
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 0, 2);

        self::assertSame(
            [0, 0, 1],
            $this->getPositionData(),
        );
    }

    public function testMoveToLowerPosition(): void
    {
        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 2, 0);

        self::assertSame(
            [1, 2, 2],
            $this->getPositionData(),
        );
    }

    public function testMoveToPositionBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position is out of range.');

        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, 9999);
    }

    public function testMoveToPositionBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Position cannot be negative.');

        $this->positionHelper->moveToPosition($this->getPositionHelperConditions(), 1, -1);
    }

    public function testRemovePosition(): void
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('nglayouts_collection_item')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('id', ':id'),
                    $query->expr()->eq('status', ':status'),
                ),
            )
            ->setParameter('id', 2, Types::INTEGER)
            ->setParameter('status', Status::Draft->value, Types::INTEGER);

        $query->executeStatement();

        $this->positionHelper->removePosition($this->getPositionHelperConditions(), 1);

        self::assertSame(
            [0, 1],
            $this->getPositionData(),
        );
    }

    public function testGetNextPosition(): void
    {
        self::assertSame(3, $this->positionHelper->getNextPosition($this->getPositionHelperConditions()));
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @return array<string, mixed>
     */
    private function getPositionHelperConditions(): array
    {
        return [
            'table' => 'nglayouts_collection_item',
            'column' => 'position',
            'conditions' => [
                'collection_id' => 1,
                'status' => Status::Draft->value,
            ],
        ];
    }

    /**
     * Returns the position data from the table under test.
     *
     * @return int[]
     */
    private function getPositionData(): array
    {
        $query = $this->databaseConnection->createQueryBuilder();
        $query->select('position')
            ->from('nglayouts_collection_item')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('collection_id', ':collection_id'),
                    $query->expr()->eq('status', ':status'),
                ),
            )
            ->setParameter('collection_id', 1, Types::INTEGER)
            ->setParameter('status', Status::Draft->value, Types::INTEGER)
            ->orderBy('position', 'ASC');

        $result = $query->fetchAllAssociative();

        return array_map('intval', array_column($result, 'position'));
    }
}
