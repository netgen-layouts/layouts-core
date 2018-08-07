<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Config;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Tests\Kernel\MockerContainer;
use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadConfigTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\Controller::checkPermissions
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadConfig::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadConfig::__invoke
     */
    public function testLoadConfig(): void
    {
        $clientContainer = $this->client->getContainer();
        if (!$clientContainer instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager */
        $tokenManager = $clientContainer->get('security.csrf.token_manager');
        $tokenId = $clientContainer->getParameter('netgen_block_manager.api.csrf_token_id');

        $currentToken = $tokenManager->getToken($tokenId);

        $this->client->request(Request::METHOD_GET, '/bm/api/v1/config');

        $response = $this->client->getResponse();

        self::assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        self::assertInternalType('array', $responseContent);
        self::assertArrayHasKey('csrf_token', $responseContent);

        self::assertInternalType('string', $responseContent['csrf_token']);
        self::assertSame($currentToken->getValue(), $responseContent['csrf_token']);
    }
}
