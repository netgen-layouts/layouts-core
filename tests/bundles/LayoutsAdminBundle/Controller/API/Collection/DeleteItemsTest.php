<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\DeleteItems;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteItems::class)]
final class DeleteItemsTest extends ApiTestCase
{
    public function testDeleteItems(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/items')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteItemsWithNonExistentCollection(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
