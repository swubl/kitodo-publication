<?php
namespace EWW\Dpf\Domain\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * InputOptionList
 */
class InputOptionList extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * displayName
     *
     * @var string
     */
    protected $displayName = '';

    /**
     * valueList
     *
     * @var string
     */
    protected $valueList = '';

    /**
     * valueLabelList
     *
     * @var string
     */
    protected $valueLabelList = '';


    /**
     * additionalValue
     *
     * @var string
     */
    protected $additionalValue = '';


    /**
     * defaultValue
     *
     * @var string
     */
    protected $defaultValue = '';

    /**
     * __construct
     */
    public function __construct()
    {

    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the displayName
     *
     * @return string $displayName
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Returns the valueList
     *
     * @return string $valueList
     */
    public function getValueList()
    {
        return $this->valueList;
    }

    /**
     * Sets the valueList
     *
     * @param string $valueList
     * @return void
     */
    public function setValueList($valueList)
    {
        $this->valueList = $valueList;
    }

    /**
     * Returns the valueLabelList
     *
     * @return string $valueLabelList
     */
    public function getValueLabelList()
    {
        return $this->valueLabelList;
    }

    /**
     * Sets the valueLabelList
     *
     * @param string $valueLabelList
     * @return void
     */
    public function setValueLabelList($valueLabelList)
    {
        $this->valueLabelList = $valueLabelList;
    }

    /**
     * Returns the additionalValueList
     *
     * @return string $additionalValueList
     */
    public function getAdditionalValueList()
    {
        return $this->additionalValueList;
    }

    /**
     * Sets the additionalValueList
     *
     * @return void
     */
    public function setAdditionalValueList($additionalValueList)
    {
        $this->additionalValueList = $additionalValueList;
    }

    /**
     * Sets the displayName
     *
     * @param string $displayName
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Returns the inputOptions
     *
     * @return array $inputOptions
     * @throws \Exception
     */
    public function getInputOptions()
    {

        return $this->_getInputOptions(FALSE);
    }


    /**
     * Returns the extended inputOptions, including the additional values
     *
     * @return array $extendedInputOptions
     * @throws \Exception
     */
    public function getExtendedInputOptions()
    {

        return $this->_getInputOptions(TRUE);
    }


    /**
     * Returns the inputOptions or extended inputOptions
     *
     * @param boolean $extended
     *
     * @return array $inputOptions
     * @throws \Exception
     */
    protected function _getInputOptions($extended = FALSE)
    {

        $values = explode("|", $this->getValueList());
        $labels = explode("|", $this->getValueLabelList());

        if (sizeof($values) != sizeof($labels)) {
            throw new \Exception('Invalid input option list configuration.');
        }

        $inputOptions = array_combine($values, $labels);

        if (!$additional) {
            return $inputOptions;
        } else {
            $additionalValueList = trim($this->getAdditionalValueList());
            if (!empty($additionalValueList)) {
                $additionalValues = explode("|", $this->getAdditionalValueList());
            }

            if (sizeof($additionalValues) != sizeof($values)) {
                throw new \Exception('Invalid input option list configuration.');
            }

            $additionalInputOptions = array();
            foreach (array_combine($values, $additionalValues) as $value => $additionalValue) {
                $additionalInputOptions[$value]['label'] = $inputOptions[$value];
                $additionalInputOptions[$value]['additionalValue'] = $additionalValue;
            }
            return $additionalInputOptions;
        }
    }


    public function setL10nParent($l10nParent)
    {

        $this->l10nParent = $l10nParent;
    }

    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Returns the defaultValue
     *
     * @return string $defaultValue
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Sets the defaultValue
     *
     * @param string $defaultValue
     * @return void
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

}
