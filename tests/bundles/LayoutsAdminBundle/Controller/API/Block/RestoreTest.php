<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Restore;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Restore::class)]
final class RestoreTest extends ApiTestCase
{
    public function testRestore(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/restore?html=false',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('blocks/restore_block');
    }

    public function testRestoreWithNonExistentBlock(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/restore',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
