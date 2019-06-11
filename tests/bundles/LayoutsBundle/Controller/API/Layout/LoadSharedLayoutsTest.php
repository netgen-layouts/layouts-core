<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadSharedLayoutsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Layout\LoadSharedLayouts::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Layout\LoadSharedLayouts::__invoke
     */
    public function testLoadSharedLayouts(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/shared_layouts',
            Response::HTTP_OK
        );
    }
}
