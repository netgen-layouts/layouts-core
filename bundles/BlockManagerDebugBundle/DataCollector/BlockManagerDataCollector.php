<?php

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Exception;

class BlockManagerDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;

        $this->data['resolved_layout'] = null;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $resolvedLayout = $this->globalVariable->getLayout();
        if ($resolvedLayout instanceof Layout) {
            $this->data['resolved_layout'] = $resolvedLayout;
        }
    }

    /**
     * Returns the resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getResolvedLayout()
    {
        return $this->data['resolved_layout'];
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'ngbm';
    }
}
