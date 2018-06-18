<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Config;

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
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager */
        $tokenManager = $this->clientContainer->get('security.csrf.token_manager');
        $tokenId = $this->clientContainer->getParameter('netgen_block_manager.api.csrf_token_id');

        $currentToken = $tokenManager->getToken($tokenId);

        $this->client->request(Request::METHOD_GET, '/bm/api/v1/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('csrf_token', $responseContent);

        $this->assertInternalType('string', $responseContent['csrf_token']);
        $this->assertSame($currentToken->getValue(), $responseContent['csrf_token']);
    }
}
