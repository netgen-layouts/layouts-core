<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API;

use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use DatabaseTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $clientContainer;

    public function setUp()
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockItemLoader();
        $this->mockItemBuilder();
        $this->mockQueryType();
        $this->createDatabase();

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function tearDown()
    {
        $this->closeDatabase();
    }

    public function setUpClient()
    {
        parent::setUpClient();

        $this->clientContainer = $this->client->getContainer();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', getenv('SF_PASSWORD'));
    }

    protected function mockItemLoader()
    {
        /** @var \Mockery\MockInterface $itemLoaderMock */
        $itemLoaderMock = $this->clientContainer->mock(
            'netgen_block_manager.item.item_loader',
            ItemLoaderInterface::class
        );

        $itemFixtures = require __DIR__ . '/fixtures/items.php';

        foreach ($itemFixtures as $value => $item) {
            $itemLoaderMock
                ->shouldReceive('load')
                ->with($item->getValue(), $item->getValueType())
                ->andReturn($item);
        }
    }

    protected function mockItemBuilder()
    {
        /** @var \Mockery\MockInterface $itemBuilderMock */
        $itemBuilderMock = $this->clientContainer->mock(
            'netgen_block_manager.item.item_builder',
            ItemBuilderInterface::class
        );

        $dynamicItemFixtures = require __DIR__ . '/fixtures/dynamic_items.php';

        foreach ($dynamicItemFixtures as $dynamicItem) {
            $itemBuilderMock
                ->shouldReceive('build')
                ->andReturnUsing(
                    function ($argument) use ($dynamicItemFixtures) {
                        return $dynamicItemFixtures[$argument->id];
                    }
                );
        }
    }

    protected function mockQueryType()
    {
        $searchFixtures = require __DIR__ . '/fixtures/search.php';

        $queryTypeRegistry = $this->clientContainer
            ->get('netgen_block_manager.collection.registry.query_type');

        $queryType = new QueryType('ezcontent_search', $searchFixtures, count($searchFixtures));
        $queryTypeRegistry->addQueryType('ezcontent_search', $queryType);
    }

    /**
     * Asserts that response is empty and has No Content status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function assertEmptyResponse(Response $response)
    {
        $this->assertEmpty($response->getContent());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertException(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, 'application/json');
        $this->assertExceptionResponse($response, $statusCode, $message);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertExceptionResponse(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        $responseContent = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $responseContent);

        $this->assertArrayHasKey('status_code', $responseContent);
        $this->assertArrayHasKey('status_text', $responseContent);

        $this->assertEquals($statusCode, $responseContent['status_code']);
        $this->assertEquals(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            $this->assertEquals($message, $responseContent['message']);
        }
    }

    /**
     * Pretty encodes the provided array.
     *
     * @param array $content
     *
     * @return string
     */
    protected function jsonEncode(array $content)
    {
        return json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
