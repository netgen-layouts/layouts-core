<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Collection;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadCollectionTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\LoadCollection::__invoke
     */
    public function testLoadCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/collections/3');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\LoadCollection::__invoke
     */
    public function testLoadCollectionWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/collections/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }
}
