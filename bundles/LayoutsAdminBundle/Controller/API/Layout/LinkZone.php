<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class LinkZone extends AbstractController
{
    use ValidatorTrait;

    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Links the provided zone to zone from shared layout.
     */
    public function __invoke(Zone $zone, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', ['layout' => $zone->getLayoutId()->toString()]);

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString($requestData->get('linked_layout_id')));

        $zoneIdentifier = $requestData->get('linked_zone_identifier');
        if (!$sharedLayout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $this->layoutService->linkZone($zone, $sharedLayout->getZone($zoneIdentifier));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
    {
        $this->validate(
            $data->get('linked_layout_id'),
            [
                new Constraints\NotBlank(),
                new Constraints\Uuid(),
            ],
            'linked_layout_id',
        );

        $this->validate(
            $data->get('linked_zone_identifier'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'linked_zone_identifier',
        );
    }
}
