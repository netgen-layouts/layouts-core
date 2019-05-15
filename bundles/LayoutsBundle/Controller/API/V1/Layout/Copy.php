<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Copy extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Copies the layout.
     */
    public function __invoke(Layout $layout, Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $copyStruct = $this->layoutService->newLayoutCopyStruct();
        $copyStruct->name = $requestData->get('name');
        $copyStruct->description = $requestData->get('description');

        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
    {
        $this->validate(
            $data->get('name'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'name'
        );

        $this->validate(
            $data->get('description'),
            [
                new Constraints\Type(['type' => 'string']),
            ],
            'description'
        );
    }
}
