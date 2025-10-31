<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Netgen\Layouts\API\Values\Collection\CollectionType;
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
                'new_type' => CollectionType::Manual->value,
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
                'new_type' => CollectionType::Dynamic->value,
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
                'new_type' => CollectionType::Manual->value,
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
                'new_type' => CollectionType::Dynamic->value,
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
    public function testChangeCollectionTypeWithInvalidQueryType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => CollectionType::Dynamic->value,
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
                'new_type' => CollectionType::Dynamic->value,
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
