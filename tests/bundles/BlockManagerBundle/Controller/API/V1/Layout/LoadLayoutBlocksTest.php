<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadLayoutBlocksTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testLoadLayoutBlocks()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/1/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testLoadLayoutBlocksInPublishedState()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/1/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testLoadLayoutBlocksWithNonExistentLayout()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/layouts/9999/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testLoadLayoutBlocksWithNonExistentLayoutLocale()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/unknown/layouts/1/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "1"'
        );
    }
}
