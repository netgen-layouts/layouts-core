<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class ConfigControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::checkPermissions
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getCsrfToken
     */
    public function testGetConfig()
    {
        $this->client->request('GET', '/bm/api/v1/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('csrf_token', $responseContent);
        $this->assertNotEmpty($responseContent['csrf_token']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getConfig
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getCsrfToken
     */
    public function testGetConfigWithInvalidToken()
    {
        /** @var \Mockery\MockInterface $tokenManagerMock */
        $tokenManagerMock = $this->clientContainer->mock(
            'security.csrf.token_manager',
            CsrfTokenManager::class
        );

        $tokenManagerMock->makePartial();

        $tokenId = $this->clientContainer->getParameter('netgen_block_manager.api.csrf_token_id');
        $invalidToken = new CsrfToken($tokenId, 'invalidToken');

        $tokenManagerMock
            ->shouldReceive('getToken')
            ->withArgs(array($tokenId))
            ->andReturn($invalidToken);

        $tokenManagerMock
            ->shouldReceive('isTokenValid')
            ->withArgs(array($invalidToken))
            ->andReturn(false);

        $tokenManagerMock
            ->shouldReceive('refreshToken')
            ->withArgs(array($tokenId))
            ->andReturn(new CsrfToken($tokenId, 'refreshedToken'));

        $this->client->request('GET', '/bm/api/v1/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('csrf_token', $responseContent);
        $this->assertEquals('refreshedToken', $responseContent['csrf_token']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getBlockTypes
     */
    public function testGetBlockTypes()
    {
        $this->client->request('GET', '/bm/api/v1/config/block_types');

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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\ConfigController::getLayoutTypes
     */
    public function testGetLayoutTypes()
    {
        $this->client->request('GET', '/bm/api/v1/config/layout_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertNotEmpty($responseContent);
    }
}
