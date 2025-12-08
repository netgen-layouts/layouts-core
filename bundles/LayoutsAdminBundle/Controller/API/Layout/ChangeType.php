<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\ZoneMappings;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ChangeType extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private LayoutService $layoutService,
        private LayoutTypeRegistry $layoutTypeRegistry,
    ) {}

    /**
     * Changes the type of the layout.
     */
    public function __invoke(Layout $layout, Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', $layout);

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $zoneMappingData = $requestData->get('zone_mappings');

        $zoneMappings = new ZoneMappings();
        foreach (($zoneMappingData ?? []) as $zone => $targetZones) {
            $zoneMappings->addZoneMapping($zone, $targetZones);
        }

        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType($requestData->get('new_type')),
            $zoneMappings,
        );

        return new View($updatedLayout);
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
                new Constraints\Type(type: 'string'),
            ],
            'new_type',
        );

        $this->validate(
            $data->get('zone_mappings'),
            [
                new Constraints\Type(type: 'associative_array'),
            ],
            'zone_mappings',
        );
    }
}
