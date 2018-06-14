<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Config;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadBlockTypesTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadBlockTypes::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadBlockTypes::__invoke
     */
    public function testLoadBlockTypes(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/config/block_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('block_types', $responseContent);
        $this->assertArrayHasKey('block_type_groups', $responseContent);

        $this->assertInternalType('array', $responseContent['block_types']);
        $this->assertNotEmpty($responseContent['block_types']);

        $this->assertInternalType('array', $responseContent['block_type_groups']);
        $this->assertNotEmpty($responseContent['block_type_groups']);
    }
}
