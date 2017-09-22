<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\BlockManager\Ez\Collection\QueryType\Handler\ContentSearchHandler;
use Netgen\BlockManager\Item\ItemLoaderInterface;
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
        $this->mockSearchHandler();
        $this->mockTranslationHelper();
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
        $this->client->setServerParameter('PHP_AUTH_USER', getenv('EZ_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', getenv('EZ_PASSWORD'));
    }

    protected function mockItemLoader()
    {
        /** @var \Mockery\MockInterface $locationMock */
        $itemLoaderMock = $this->clientContainer->mock(
            'netgen_block_manager.item.item_loader',
            ItemLoaderInterface::class
        );

        $itemFixtures = require __DIR__ . '/fixtures/items.php';

        foreach ($itemFixtures as $valueId => $item) {
            $itemLoaderMock
                ->shouldReceive('load')
                ->with($item->getValueId(), $item->getValueType())
                ->andReturn($item);
        }
    }

    protected function mockSearchHandler()
    {
        /** @var \Mockery\MockInterface $searchHandlerMock */
        $searchHandlerMock = $this->clientContainer->mock(
            'netgen_block_manager.collection.query_type.handler.ezcontent_search',
            ContentSearchHandler::class
        );

        $searchHandlerMock->makePartial();

        $searchFixtures = require __DIR__ . '/fixtures/search.php';

        $searchHandlerMock
            ->shouldReceive('getCount')
            ->andReturn(count($searchFixtures));

        $searchHandlerMock
            ->shouldReceive('getValues')
            ->andReturn($searchFixtures);
    }

    protected function mockTranslationHelper()
    {
        /** @var \Mockery\MockInterface $translationHelperMock */
        $translationHelperMock = $this->clientContainer->mock(
            'ezpublish.translation_helper',
            TranslationHelper::class
        );

        $translationHelperMock
            ->shouldReceive('getTranslatedContentNameByContentInfo')
            ->andReturnUsing(
                function (ContentInfo $contentInfo) {
                    return $contentInfo->name;
                }
            );
    }

    /**
     * Asserts that response has JSON content.
     * If filename is set, asserts that response content matches the one in given file.
     * If statusCode is set, asserts that response has given status code.
     * If excludeElements is set, the items in it will not be checked for value equality but only for existence.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $filename
     * @param int $statusCode
     * @param array $excludedElements
     */
    protected function assertResponse(Response $response, $filename, $statusCode = Response::HTTP_OK, $excludedElements = array())
    {
        $responseContent = json_decode($response->getContent(), true);
        if (is_array($responseContent)) {
            foreach ($excludedElements as $excludedElement) {
                $this->assertArrayHasKey($excludedElement, $responseContent);
                $this->assertNotEmpty($responseContent[$excludedElement]);
                unset($responseContent[$excludedElement]);
            }

            $response->setContent(json_encode($responseContent));
        }

        parent::assertResponse($response, $filename, $statusCode);
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
        return json_encode($content, JSON_PRETTY_PRINT);
    }
}
