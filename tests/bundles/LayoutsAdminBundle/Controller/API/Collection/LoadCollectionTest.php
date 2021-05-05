<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadCollectionTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollection::__invoke
     */
    public function testLoadCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/da050624-8ae0-5fb9-ae85-092bf8242b89');

        $this->assertResponse(
            $this->client->getResponse(),
            'collections/load_collection',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollection::__invoke
     */
    public function testLoadCollectionWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
