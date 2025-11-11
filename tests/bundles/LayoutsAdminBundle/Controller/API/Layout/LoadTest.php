<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Load;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Load::class)]
final class LoadTest extends JsonApiTestCase
{
    public function testLoad(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_layout',
            Response::HTTP_OK,
        );
    }

    public function testLoadInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/load_published_layout',
            Response::HTTP_OK,
        );
    }

    public function testLoadWithNonExistentLayout(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
