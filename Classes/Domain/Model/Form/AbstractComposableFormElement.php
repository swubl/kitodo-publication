<?php
namespace EWW\Dpf\Domain\Model\Form;


use EWW\Dpf\Domain\Model\Form\FormElementInterface;
use EWW\Dpf\Domain\Model\Form\AbstractFormElement;


class AbstractComposableFormElement extends AbstractFormElement
{

    protected $formElements = array();


    public function addFormElement(FormElementInterface $formElement) {

        $formElement->setIndex(count($this->formElements));
        $formElement->setParentFormElement($this);
        $this->formElements[] = $formElement;
    }

}