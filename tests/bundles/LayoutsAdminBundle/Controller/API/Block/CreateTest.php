<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Create;
use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Create::class)]
#[CoversClass(CreateStructBuilder::class)]
final class CreateTest extends ApiTestCase
{
    public function testCreate(): void
    {
        $data = [
            'block_type' => 'list',
            'parent_placeholder' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block');
    }

    public function testCreateWithViewType(): void
    {
        $data = [
            'block_type' => 'grid',
            'parent_placeholder' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block_with_view_type');
    }

    public function testCreateWithItemViewType(): void
    {
        $data = [
            'block_type' => 'grid',
            'parent_placeholder' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block_with_item_view_type');
    }

    public function testCreateWithNoPosition(): void
    {
        $data = [
            'block_type' => 'list',
            'parent_placeholder' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block_at_end');
    }

    public function testCreateWithNonContainerTargetBlock(): void
    {
        $data = [
            'block_type' => 'list',
            'parent_placeholder' => 'main',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "targetBlock" has an invalid state. Target block is not a container.');
    }

    public function testCreateWithContainerInsideContainer(): void
    {
        $data = [
            'block_type' => 'column',
            'parent_placeholder' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.');
    }

    public function testCreateWithNonExistentBlockType(): void
    {
        $data = [
            'block_type' => 'unknown',
            'parent_placeholder' => 'main',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "block_type" has an invalid state. Block type does not exist.');
    }

    public function testCreateWithNonExistentPlaceholder(): void
    {
        $data = [
            'block_type' => 'title',
            'parent_placeholder' => 'unknown',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');
    }

    public function testCreateWithOutOfRangePosition(): void
    {
        $data = [
            'block_type' => 'list',
            'parent_placeholder' => 'left',
            'parent_position' => 9999,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }
}
