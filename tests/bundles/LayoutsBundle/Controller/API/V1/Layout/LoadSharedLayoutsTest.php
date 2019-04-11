<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadSharedLayoutsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LoadSharedLayouts::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\LoadSharedLayouts::__invoke
     */
    public function testLoadSharedLayouts(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/layouts/shared');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/layouts/shared_layouts',
            Response::HTTP_OK
        );
    }
}
