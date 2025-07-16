<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\App\Item\Value;
use Netgen\Layouts\Tests\App\MockerContainer;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use Netgen\Layouts\Tests\TestCase\LegacyTestCaseTrait;
use Symfony\Component\HttpFoundation\Response;

use function count;
use function getenv;
use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use DatabaseTrait;
    use LegacyTestCaseTrait;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockQueryType();
        $this->createDatabase();

        $this->expectedResponsesPath = __DIR__ . '/_responses/expected';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

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
        $container = $this->client->getKernel()->getContainer();
        if (!$container instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        $searchResults = [new Value(140), new Value(79), new Value(78)];

        /** @var \Netgen\Layouts\Collection\Registry\QueryTypeRegistry $queryTypeRegistry */
        $queryTypeRegistry = $container->get('netgen_layouts.collection.registry.query_type');

        $queryType = new QueryType('my_query_type', $searchResults, count($searchResults));
        $allQueryTypes = $queryTypeRegistry->getQueryTypes();
        $allQueryTypes['my_query_type'] = $queryType;

        $container->mock(
            'netgen_layouts.collection.registry.query_type',
            new QueryTypeRegistry($allQueryTypes),
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
        $responseContent = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($responseContent);

        self::assertArrayHasKey('status_code', $responseContent);
        self::assertArrayHasKey('status_text', $responseContent);

        self::assertSame($statusCode, $responseContent['status_code']);
        self::assertSame(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            $message !== '' && $message[0] === '/' && $message[-1] === '/' ?
                self::assertPatternMatchesRegularExpression($message, $responseContent['message']) :
                self::assertSame($message, $responseContent['message']);
        }
    }

    /**
     * Pretty encodes the provided array.
     *
     * @param mixed[] $content
     */
    protected function jsonEncode(array $content): string
    {
        return json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
