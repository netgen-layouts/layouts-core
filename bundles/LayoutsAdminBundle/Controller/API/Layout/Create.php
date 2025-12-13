<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Create extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private LayoutService $layoutService,
        private LayoutTypeRegistry $layoutTypeRegistry,
    ) {}

    /**
     * Creates the layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout type does not exist
     */
    public function __invoke(Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($requestData->getString('layout_type'));
        } catch (LayoutTypeException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            $requestData->getString('name'),
            $requestData->getString('locale'),
        );

        $layoutCreateStruct->description = $requestData->getString('description');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided input bag.
     *
     * @param \Symfony\Component\HttpFoundation\InputBag<int|string> $data
     */
    private function validateRequestData(InputBag $data): void
    {
        $this->validate(
            $data->get('layout_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'string'),
            ],
            'layout_type',
        );

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

        $this->validate(
            $data->get('locale'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'string'),
                new Constraints\Locale(canonicalize: false),
            ],
            'locale',
        );
    }
}
