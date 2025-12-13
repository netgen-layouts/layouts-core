<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Copy extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private LayoutService $layoutService,
    ) {}

    /**
     * Copies the layout.
     */
    public function __invoke(Layout $layout, Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $copyStruct = $this->layoutService->newLayoutCopyStruct();
        $copyStruct->name = $requestData->getString('name');
        $copyStruct->description = $requestData->get('description');

        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        return new View($copiedLayout, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided input bag.
     *
     * @param \Symfony\Component\HttpFoundation\InputBag<int|string> $data
     */
    private function validateRequestData(InputBag $data): void
    {
        $this->validate(
            $data->get('name'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'string'),
            ],
            'name',
        );

        if ($data->has('description')) {
            $this->validate(
                $data->get('description'),
                [
                    new Constraints\NotNull(),
                    new Constraints\Type(type: 'string'),
                ],
                'description',
            );
        }
    }
}
