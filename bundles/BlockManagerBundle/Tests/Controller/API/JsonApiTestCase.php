<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API;

use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;
use Symfony\Component\HttpFoundation\Response;
use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Lakion\ApiTestCase\MediaTypes;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use DatabaseTrait;

    public function setUp()
    {
        parent::setUp();

        $this->setUpClient();

        $this->prepareDatabase(__DIR__ . '/../../../../../lib/Tests/_fixtures');

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function setUpClient()
    {
        parent::setUpClient();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * Asserts that response has JSON content.
     * If filename is set, asserts that response content matches the one in given file.
     * If statusCode is set, asserts that response has given status code.
     * If excludeElements is set, the items in it will not be checked for value equality
     *     but only for existence. This is useful in our case when response has a rendered HTML.
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
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     */
    protected function assertException(Response $response, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, MediaTypes::JSON);
        $this->assertExceptionResponseMessage($response, $statusCode);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     */
    protected function assertExceptionResponseMessage(Response $response, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $responseContent = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $responseContent);

        $this->assertArrayHasKey('status_code', $responseContent);
        $this->assertArrayHasKey('status_text', $responseContent);

        $this->assertEquals($responseContent['status_code'], $statusCode);
        $this->assertEquals($responseContent['status_text'], Response::$statusTexts[$statusCode]);
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
