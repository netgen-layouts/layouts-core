<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LoadTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoad()
    {
        $this->client->request('GET', '/bm/api/v1/en/blocks/31?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoadInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/en/blocks/31?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_published_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoadWithNonExistentBlock()
    {
        $this->client->request('GET', '/bm/api/v1/en/blocks/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }
}
