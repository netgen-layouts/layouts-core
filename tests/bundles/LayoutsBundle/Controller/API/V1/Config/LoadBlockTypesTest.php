<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Config;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadBlockTypesTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config\LoadBlockTypes::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config\LoadBlockTypes::__invoke
     */
    public function testLoadBlockTypes(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/config/block_types');

        $response = $this->client->getResponse();

        self::assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        self::assertIsArray($responseContent);
        self::assertArrayHasKey('block_types', $responseContent);
        self::assertArrayHasKey('block_type_groups', $responseContent);

        self::assertIsArray($responseContent['block_types']);
        self::assertNotEmpty($responseContent['block_types']);

        self::assertIsArray($responseContent['block_type_groups']);
        self::assertNotEmpty($responseContent['block_type_groups']);
    }
}
