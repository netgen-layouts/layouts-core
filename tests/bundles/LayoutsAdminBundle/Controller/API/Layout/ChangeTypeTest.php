<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeTypeTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__invoke
     */
    public function testChangeType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
                'zone_mappings' => [
                    'left' => ['left'],
                    'right' => ['right'],
                ],
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/change_type',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithInvalidNewType(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => 42,
                'zone_mappings' => [
                    'left' => ['left'],
                    'right' => ['right'],
                ],
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "new_type": This value should be of type string.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithMissingNewType(): void
    {
        $data = $this->jsonEncode(
            [
                'zone_mappings' => [
                    'left' => ['left'],
                    'right' => ['right'],
                ],
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithInvalidMappings(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
                'zone_mappings' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zone_mappings": This value should be of type array.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithNoMappings(): void
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/change_type_without_mappings',
            Response::HTTP_OK,
        );
    }
}
