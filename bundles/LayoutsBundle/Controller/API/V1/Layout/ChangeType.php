<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ChangeType extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutService $layoutService, LayoutTypeRegistry $layoutTypeRegistry)
    {
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Changes the type of the layout.
     */
    public function __invoke(Layout $layout, Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', $layout);

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $zoneMappings = $requestData->get('zone_mappings');

        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType($requestData->get('new_type')),
            $zoneMappings ?? []
        );

        return new View($updatedLayout, Version::API_V1);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
    {
        $this->validate(
            $data->get('new_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'new_type'
        );

        $this->validate(
            $data->get('zone_mappings'),
            [
                new Constraints\Type(['type' => 'array']),
            ],
            'zone_mappings'
        );
    }
}
