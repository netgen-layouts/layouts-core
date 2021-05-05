<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Netgen\Layouts\API\Values\Collection\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeCollectionTypeTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeFromManualToManual(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_MANUAL,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
                'query_type' => 'my_query_type',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_MANUAL,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/featured/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeFromDynamicToDynamic(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
                'query_type' => 'my_query_type',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/featured/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeWithInvalidNewType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '1',
                'query_type' => 'my_query_type',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "new_type": The value you selected is not a valid choice.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeWithMissingNewType(): void
    {
        $data = $this->jsonEncode(
            [
                'query_type' => 'my_query_type',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "new_type": This value should not be blank.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeWithInvalidQueryType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
                'query_type' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "query_type": This value should be of type string.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType::validateRequestData
     */
    public function testChangeCollectionTypeWithMissingQueryType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "query_type": This value should not be blank.',
        );
    }
}
