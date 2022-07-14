<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;

use const JSON_THROW_ON_ERROR;

final class LoadBlockTypesTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadBlockTypes::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadBlockTypes::__invoke
     */
    public function testLoadBlockTypes(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/config/block_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($responseContent);
        self::assertArrayHasKey('block_types', $responseContent);
        self::assertArrayHasKey('block_type_groups', $responseContent);

        self::assertIsArray($responseContent['block_types']);
        self::assertNotEmpty($responseContent['block_types']);

        self::assertIsArray($responseContent['block_type_groups']);
        self::assertNotEmpty($responseContent['block_type_groups']);
    }
}
