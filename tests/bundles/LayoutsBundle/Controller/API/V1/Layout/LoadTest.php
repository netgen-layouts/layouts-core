<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoad(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoadInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoadWithNonExistentLayout(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
