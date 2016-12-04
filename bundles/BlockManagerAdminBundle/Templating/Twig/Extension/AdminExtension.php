<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;

class AdminExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'ngbm_admin' => $this->globalVariable,
        );
    }
}
