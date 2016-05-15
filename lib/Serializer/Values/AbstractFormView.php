<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFormView extends AbstractView
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param mixed $value
     * @param int $version
     * @param int $statusCode
     */
    public function __construct(FormInterface $form, $value, $version, $statusCode = Response::HTTP_OK)
    {
        parent::__construct($value, $version, $statusCode);

        $this->form = $form;
    }

    /**
     * Returns the form.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
