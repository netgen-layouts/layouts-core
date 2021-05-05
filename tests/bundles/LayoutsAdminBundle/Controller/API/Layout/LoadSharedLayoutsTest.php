<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadSharedLayoutsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadSharedLayouts::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadSharedLayouts::__invoke
     */
    public function testLoadSharedLayouts(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/shared_layouts',
            Response::HTTP_OK,
        );
    }
}
