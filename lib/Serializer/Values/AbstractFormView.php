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
     * @var string
     */
    protected $formName;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param string $formName
     * @param mixed $value
     * @param int $version
     * @param int $statusCode
     */
    public function __construct(FormInterface $form, $formName, $value, $version, $statusCode = Response::HTTP_OK)
    {
        parent::__construct($value, $version, $statusCode);

        $this->form = $form;
        $this->formName = $formName;
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

    /**
     * Returns the form name.
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->formName;
    }
}
