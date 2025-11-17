<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\ChangeCollectionType;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use Netgen\Layouts\API\Values\Collection\CollectionType;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ChangeCollectionType::class)]
final class ChangeCollectionTypeTest extends ApiTestCase
{
    public function testChangeCollectionTypeFromManualToManual(): void
    {
        $data = [
            'new_type' => CollectionType::Manual->value,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testChangeCollectionTypeFromManualToDynamic(): void
    {
        $data = [
            'new_type' => CollectionType::Dynamic->value,
            'query_type' => 'my_query_type',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testChangeCollectionTypeFromDynamicToManual(): void
    {
        $data = [
            'new_type' => CollectionType::Manual->value,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/featured/change_type',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testChangeCollectionTypeFromDynamicToDynamic(): void
    {
        $data = [
            'new_type' => CollectionType::Dynamic->value,
            'query_type' => 'my_query_type',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/featured/change_type',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testChangeCollectionTypeWithInvalidQueryType(): void
    {
        $data = [
            'new_type' => CollectionType::Dynamic->value,
            'query_type' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "query_type": This value should be of type string.');
    }

    public function testChangeCollectionTypeWithMissingQueryType(): void
    {
        $data = [
            'new_type' => CollectionType::Dynamic->value,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/change_type',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "query_type": This value should not be blank.');
    }
}
