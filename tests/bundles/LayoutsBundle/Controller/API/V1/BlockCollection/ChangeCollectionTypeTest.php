<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ChangeCollectionTypeTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::validateChangeCollectionType
     */
    public function testChangeCollectionTypeFromManualToManual(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_MANUAL,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/collections/default/change_type',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::validateChangeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
                'query_type' => 'my_query_type',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/collections/default/change_type',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::validateChangeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_MANUAL,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/collections/featured/change_type',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::__invoke
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection\ChangeCollectionType::validateChangeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToDynamic(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => Collection::TYPE_DYNAMIC,
                'query_type' => 'my_query_type',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/en/blocks/31/collections/featured/change_type',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }
}
