<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeTypeTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__invoke
     */
    public function testChangeType()
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
                'zone_mappings' => [
                    'left' => ['left'],
                    'right' => ['right'],
                ],
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/change_type?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithoutMappings()
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/change_type?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type_without_mappings',
            Response::HTTP_OK
        );
    }
}
