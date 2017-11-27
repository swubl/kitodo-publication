<?php
namespace EWW\Dpf\Domain\Model\Form;

use EWW\Dpf\Domain\Model\Form\FormElementInterface;


class AbstractFormElement implements FormElementInterface
{

    /**
     * The parent formElement
     *
     * @var FormElementInterface
     */
    protected $parentFormElement = NULL;

    /**
     * The index inside the parent formElement
     *
     * @var int
     */
    protected $index = 0;


    /**
     * The identifier of the formElement
     *
     * @var string
     */
    protected $identifier;


    /**
     * Return parent formElement
     *
     * @return null|FormElementInterface parent formElement
     * @internal
     */
    public function getParentFormElement()
    {
        return $this->parentFormElement;
    }

    /**
     * Set parent formElement.
     *
     * @param FormElementInterface $formElement
     * @internal
     */
    public function setParentFormElement(FormElementInterface $formElement)
    {
        $this->parentFormElement = $formElement;
    }

    /**
     * Set index of this formElement inside the parent formElement
     *
     * @param int $index
     * @internal
     */
    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    /**
     * Get the index inside the parent formElement
     *
     * @return int
     * @api
     */
    public function getIndex()
    {
        return $this->index;
    }


    /**
     * Get the identifier of the formElement
     *
     * @return string
     * @api
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the identifier of the formElement
     *
     * @param string $identifier
     * @api
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }
}