<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItems;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadItems::class)]
final class LoadItemsTest extends ApiTestCase
{
    public function testLoadItems(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/da050624-8ae0-5fb9-ae85-092bf8242b89/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('collections/load_collection_items');
    }

    public function testLoadItemsWithNonExistentCollection(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
