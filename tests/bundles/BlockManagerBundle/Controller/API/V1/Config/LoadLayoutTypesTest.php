<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Config;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadLayoutTypesTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__invoke
     */
    public function testLoadLayoutTypes()
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/config/layout_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertNotEmpty($responseContent);
    }
}
