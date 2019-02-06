<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Config;

use Netgen\BlockManager\Tests\TestCase\LegacyTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadLayoutTypesTest extends JsonApiTestCase
{
    use LegacyTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__invoke
     */
    public function testLoadLayoutTypes(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/config/layout_types');

        $response = $this->client->getResponse();

        self::assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        self::assertIsArray($responseContent);
        self::assertNotEmpty($responseContent);
    }
}
