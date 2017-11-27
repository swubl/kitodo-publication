<?php
namespace EWW\Dpf\Domain\Model\Form;


interface FormElementInterface
{
    /**
     * Return parent formElement
     *
     * @return null|FormElementInterface parent formElement
     * @internal
     */
    public function getParentFormElement();

    /**
     * Set parent formElement.
     *
     * @param FormElementInterface $formElement
     * @internal
     */
    public function setParentFormElement(FormElementInterface $formElement);

    /**
     * Set index of this formElement inside the parent formElement
     *
     * @param int $index
     * @internal
     */
    public function setIndex(int $index);

    /**
     * Get the index inside the parent formElement
     *
     * @return int
     * @api
     */
    public function getIndex();
}