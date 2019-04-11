<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller;

use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseAbstractController
{
    public static function getSubscribedServices(): array
    {
        return [
            'netgen_block_manager.view.view_builder' => ViewBuilderInterface::class,
        ] + parent::getSubscribedServices();
    }

    /**
     * Builds the view from provided value.
     *
     * @param mixed $value
     * @param string $context
     * @param array<string, mixed> $parameters
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Netgen\Layouts\View\ViewInterface
     */
    protected function buildView(
        $value,
        string $context = ViewInterface::CONTEXT_DEFAULT,
        array $parameters = [],
        ?Response $response = null
    ): ViewInterface {
        /** @var \Netgen\Layouts\View\ViewBuilderInterface $viewBuilder */
        $viewBuilder = $this->get('netgen_block_manager.view.view_builder');
        $view = $viewBuilder->buildView($value, $context, $parameters);

        $view->setResponse($response instanceof Response ? $response : new Response());

        return $view;
    }
}
