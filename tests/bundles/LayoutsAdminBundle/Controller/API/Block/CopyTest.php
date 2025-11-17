<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Copy;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Copy::class)]
final class CopyTest extends ApiTestCase
{
    public function testCopy(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/copy_block');
    }

    public function testCopyWithNonExistentBlock(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/copy',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testCopyWithNonExistentTargetBlock(): void
    {
        $data = [
            // This is a random UUID.
            'parent_block_id' => 'cbdb1617-9a2c-48e3-9870-d0f707dbff1f',
            'parent_placeholder' => 'main',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "cbdb1617-9a2c-48e3-9870-d0f707dbff1f"');
    }

    public function testCopyWithNonExistentPlaceholder(): void
    {
        $data = [
            'parent_block_id' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
            'parent_placeholder' => 'unknown',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.');
    }

    public function testCopyWithNonContainerTargetBlock(): void
    {
        $data = [
            'parent_block_id' => '129f51de-a535-5094-8517-45d672e06302',
            'parent_placeholder' => 'main',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "targetBlock" has an invalid state. Target block is not a container.');
    }

    public function testCopyWithContainerInsideContainer(): void
    {
        $data = [
            'parent_block_id' => 'a2806e8a-ea8c-5c3b-8f84-2cbdae1a07f6',
            'parent_placeholder' => 'main',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/e666109d-f1db-5fd5-97fa-346f50e9ae59/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "block" has an invalid state. Containers cannot be placed inside containers.');
    }

    public function testCopyWithInvalidBlockId(): void
    {
        $data = [
            'parent_block_id' => 42,
            'parent_placeholder' => 'main',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "parent_block_id": This value is not a valid UUID.');
    }

    public function testCopyWithMissingBlockId(): void
    {
        $data = [
            'parent_placeholder' => 'main',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/42446cc9-24c3-573c-9022-6b3a764727b5/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "parent_block_id": This value should not be blank.');
    }
}
