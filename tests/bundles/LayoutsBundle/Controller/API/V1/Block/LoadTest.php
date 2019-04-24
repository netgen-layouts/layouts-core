<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoad(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoadInPublishedState(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_published_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Load::__invoke
     */
    public function testLoadWithNonExistentBlock(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
