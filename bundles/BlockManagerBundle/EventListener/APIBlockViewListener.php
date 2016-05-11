<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

class APIBlockViewListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(
        BlockService $blockService,
        ConfigurationInterface $configuration,
        FormFactoryInterface $formFactory
    ) {
        $this->blockService = $blockService;
        $this->configuration = $configuration;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Adds an inline edit form to API block views.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface || $view->getContext() !== ViewInterface::CONTEXT_API) {
            return;
        }

        $block = $view->getBlock();
        $blockConfig = $this->configuration->getBlockConfig($block->getDefinitionIdentifier());

        if (!isset($blockConfig['forms']['inline_edit'])) {
            return;
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->formFactory->create(
            $blockConfig['forms']['inline_edit'],
            $updateStruct,
            array('block' => $block)
        );

        $event->getParameterBag()->set('form', $form->createView());
    }
}
