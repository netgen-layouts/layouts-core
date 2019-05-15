<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RestoreTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Restore::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Restore::__invoke
     */
    public function testRestore(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/28df256a-2467-5527-b398-9269ccc652de/restore?html=false',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/restore_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Restore::__invoke
     */
    public function testRestoreWithNonExistentBlock(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/restore',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
