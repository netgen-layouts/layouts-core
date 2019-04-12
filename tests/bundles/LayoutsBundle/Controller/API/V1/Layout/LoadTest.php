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
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/1?html=false');

        self::assertResponse(
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
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/1?published=true&html=false');

        self::assertResponse(
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
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }
}
