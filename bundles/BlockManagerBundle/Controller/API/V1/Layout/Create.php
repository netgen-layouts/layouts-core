<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Create extends Controller
{
    use ValidatorTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(
        LayoutService $layoutService,
        LayoutTypeRegistryInterface $layoutTypeRegistry
    ) {
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Creates the layout.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout type does not exist
     */
    public function __invoke(Request $request): View
    {
        $requestData = $request->attributes->get('data');

        $this->validateCreateLayout($requestData);

        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($requestData->get('layout_type'));
        } catch (LayoutTypeException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            $requestData->get('name'),
            $requestData->get('locale')
        );

        $layoutCreateStruct->description = $requestData->get('description');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Validates layout creation parameters from the request.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    private function validateCreateLayout(ParameterBag $data): void
    {
        $this->validate(
            $data->get('layout_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'layout_type'
        );

        $this->validate(
            $data->get('locale'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new LocaleConstraint(),
            ],
            'locale'
        );
    }
}
