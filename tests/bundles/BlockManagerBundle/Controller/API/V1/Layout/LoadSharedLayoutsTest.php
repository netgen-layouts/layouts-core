<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadSharedLayoutsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadSharedLayouts::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadSharedLayouts::__invoke
     */
    public function testLoadSharedLayouts()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/shared_layouts',
            Response::HTTP_OK
        );
    }
}
