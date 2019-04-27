<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\App\Item\Value;
use Netgen\Layouts\Tests\App\MockerContainer;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use DatabaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockQueryType();
        $this->createDatabase();

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    public function setUpClient(): void
    {
        parent::setUpClient();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', (string) getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', (string) getenv('SF_PASSWORD'));
    }

    protected function mockQueryType(): void
    {
        $clientContainer = $this->client->getContainer();
        if (!$clientContainer instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        $searchResults = [new Value(140), new Value(79), new Value(78)];

        /** @var \Netgen\Layouts\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry */
        $queryTypeRegistry = $clientContainer->get('netgen_layouts.collection.registry.query_type');

        $queryType = new QueryType('my_query_type', $searchResults, count($searchResults));
        $allQueryTypes = $queryTypeRegistry->getQueryTypes();
        $allQueryTypes['my_query_type'] = $queryType;

        $clientContainer->mock(
            'netgen_layouts.collection.registry.query_type',
            new QueryTypeRegistry($allQueryTypes)
        );
    }

    /**
     * Asserts that response is empty and has No Content status code.
     */
    protected function assertEmptyResponse(Response $response): void
    {
        self::assertEmpty($response->getContent());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     */
    protected function assertException(Response $response, int $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null): void
    {
        if (($_SERVER['OPEN_ERROR_IN_BROWSER'] ?? false) === true) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, 'application/json');
        $this->assertExceptionResponse($response, $statusCode, $message);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     */
    protected function assertExceptionResponse(Response $response, int $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null): void
    {
        $responseContent = json_decode($response->getContent(), true);
        self::assertIsArray($responseContent);

        self::assertArrayHasKey('status_code', $responseContent);
        self::assertArrayHasKey('status_text', $responseContent);

        self::assertSame($statusCode, $responseContent['status_code']);
        self::assertSame(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            self::assertSame($message, $responseContent['message']);
        }
    }

    /**
     * Pretty encodes the provided array.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If encoding failed
     */
    protected function jsonEncode(array $content): string
    {
        $encodedContent = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($encodedContent)) {
            throw new RuntimeException(
                sprintf(
                    'There was an error encoding the value: %s',
                    json_last_error_msg()
                )
            );
        }

        return $encodedContent;
    }
}
