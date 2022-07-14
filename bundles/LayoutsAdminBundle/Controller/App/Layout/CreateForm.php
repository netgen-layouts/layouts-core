<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Form\CreateType;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_key_first;
use function count;

final class CreateForm extends AbstractController
{
    private LayoutService $layoutService;

    private LocaleProviderInterface $localeProvider;

    public function __construct(LayoutService $layoutService, LocaleProviderInterface $localeProvider)
    {
        $this->layoutService = $layoutService;
        $this->localeProvider = $localeProvider;
    }

    /**
     * Displays and processes layout create form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $availableLocales = $this->localeProvider->getAvailableLocales();
        if (count($availableLocales) === 0) {
            throw new RuntimeException('There are no available locales configured in the system.');
        }

        $createStruct = new LayoutCreateStruct();
        $createStruct->mainLocale = (string) array_key_first($availableLocales);

        $form = $this->createForm(
            CreateType::class,
            $createStruct,
            [
                'action' => $this->generateUrl(
                    'nglayouts_app_layout_form_create',
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_APP);
        }

        if ($form->isValid()) {
            $createdLayout = $this->layoutService->createLayout($createStruct);

            return $this->json(
                [
                    'id' => $createdLayout->getId()->toString(),
                ],
                Response::HTTP_CREATED,
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_APP,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
