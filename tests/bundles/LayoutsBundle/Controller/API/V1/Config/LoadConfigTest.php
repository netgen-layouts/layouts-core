<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Config;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\App\MockerContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadConfigTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config\LoadConfig::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config\LoadConfig::__invoke
     */
    public function testLoadConfig(): void
    {
        $clientContainer = $this->client->getContainer();
        if (!$clientContainer instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager */
        $tokenManager = $clientContainer->get('security.csrf.token_manager');
        $tokenId = $clientContainer->getParameter('netgen_layouts.api.csrf_token_id');

        $currentToken = $tokenManager->getToken($tokenId);

        $this->client->request(Request::METHOD_GET, '/nglayouts/api/v1/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        self::assertIsArray($responseContent);
        self::assertArrayHasKey('csrf_token', $responseContent);

        self::assertIsString($responseContent['csrf_token']);
        self::assertSame($currentToken->getValue(), $responseContent['csrf_token']);
    }
}
