<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Move;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Move::class)]
final class MoveTest extends ApiTestCase
{
    public function testMove(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testMoveToDifferentPlaceholder(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'right',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/129f51de-a535-5094-8517-45d672e06302/move',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testMoveToDifferentBlock(): void
    {
        $data = [
            'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
            'parent_placeholder' => 'main',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/129f51de-a535-5094-8517-45d672e06302/move',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testMoveWithNonExistentBlock(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/move/zone',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testMoveWithNonExistentTargetBlock(): void
    {
        $data = [
            // This is a random UUID.
            'parent_block_id' => '2c9e3553-8fa5-49f7-9672-e5a5218ce812',
            'parent_placeholder' => 'main',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "2c9e3553-8fa5-49f7-9672-e5a5218ce812"');
    }

    public function testMoveWithNonExistentPlaceholder(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'unknown',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');
    }

    public function testMoveWithNonContainerTargetBlock(): void
    {
        $data = [
            'parent_block_id' => 'b07d3a85-bcdb-5af2-9b6f-deba36c700e7',
            'parent_placeholder' => 'main',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "targetBlock" has an invalid state. Target block is not a container.');
    }

    public function testMoveWithOutOfRangePosition(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'left',
            'parent_position' => 9999,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }

    public function testMoveWithContainerInsideContainer(): void
    {
        $data = [
            'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
            'parent_placeholder' => 'main',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "block" has an invalid state. Containers cannot be placed inside containers.');
    }

    public function testMoveWithInvalidBlockId(): void
    {
        $data = [
            'parent_block_id' => 42,
            'parent_placeholder' => 'main',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "parent_block_id": This value is not a valid UUID.');
    }

    public function testMoveWithMissingBlockId(): void
    {
        $data = [
            'parent_placeholder' => 'main',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/b07d3a85-bcdb-5af2-9b6f-deba36c700e7/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "parent_block_id": This value should not be blank.');
    }
}
