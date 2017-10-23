<?php

namespace Netgen\BlockManager\Collection\Result\Pagerfanta\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Twig\Environment;

class CollectionPagerView implements ViewInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $template;

    /**
     * @param \Twig\Environment $twig
     * @param $template
     */
    public function __construct(Environment $twig, $template)
    {
        $this->twig = $twig;
        $this->template = $template;
    }

    public function getName()
    {
        return 'ngbm_collection_pager';
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $pagerTemplate = $this->template;
        if (array_key_exists('template', $options)) {
            $pagerTemplate = $options['template'];
            unset($options['template']);
        }

        return $this->twig->render(
            $pagerTemplate,
            array(
                'pager' => $pagerfanta,
                'pager_uri' => $routeGenerator(1),
            ) + $options
        );
    }
}
