<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItem;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadItem::class)]
final class LoadItemTest extends ApiTestCase
{
    public function testLoadItem(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/items/89c214a3-204f-5352-85d7-8852b26ab6b0')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('collections/load_item');
    }

    public function testLoadItemWithNonExistentItem(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
