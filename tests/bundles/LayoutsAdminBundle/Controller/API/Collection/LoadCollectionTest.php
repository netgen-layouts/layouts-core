<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollection;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadCollection::class)]
final class LoadCollectionTest extends ApiTestCase
{
    public function testLoadCollection(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/da050624-8ae0-5fb9-ae85-092bf8242b89')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('collections/load_collection');
    }

    public function testLoadCollectionWithNonExistentCollection(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
