<?php
namespace EWW\Dpf\Domain\Persistence;

class FormPersistenceManager
{

    /**
     * documentTypeRepository
     *
     * @var \EWW\Dpf\Domain\Repository\DocumentTypeRepository
     * @inject
     */
    protected $documentTypeRepository = null;

    /**
     *  Reads the definition of a document form from the database
     *
     * @param string $documentTypeName
     *
     * @return array
     */
    public function getFormDefinition($documentTypeName)
    {
        $formDefinition = array();

        $documentTypeResult = $this->documentTypeRepository->findByName($documentTypeName);

        if (is_object($documentTypeResult) && $documentTypeResult->count() == 1) {

            $documentType = $documentTypeResult[0];

            $formDefinition['type'] = "Form";
            $formDefinition['identifier'] = $documentType->getName();
            $formDefinition['label'] = $documentType->getDisplayName();
            $formDefinition['formElements'] = array();

            $metadataPages = $documentType->getMetadataPage();
            foreach ($metadataPages as $metadataPage) {

                $formPage = array();
                $formPage['type'] = "Page";
                $formPage['identifier'] = $metadataPage->getUID();
                $formPage['name'] = $metadataPage->getName();
                $formPage['label'] = $metadataPage->getDisplayName();
                $formPage['permission'] = $this->getPermision($metadataPage->getBackendOnly());

                $metadataGroups = $metadataPage->getMetadataGroup();
                foreach ($metadataGroups as $metadataGroup) {

                    $formGroup = $this->getGroupDefinition($metadataGroup);

                    $metadataObjects = $metadataGroup->getMetadataObject();
                    foreach ($metadataObjects as $metadataObject) {
                        $formField = $this->getFieldDefinition($metadataObject);
                        $formGroup['formElements'][] = $formField;
                    }

                    $formPage['formElements'][] = $formGroup;
                }

                $formDefinition['formElements'][] = $formPage;
            }
        } else {
            throw new \Exception("Error while loading form definition by document type: ".$documentTypeName);
        }

        return $formDefinition;
    }

    /**
     * Reads the definition of a form group without child elements
     *
     * @param \EWW\Dpf\Domain\Model\MetadataPage $metadataGroup
     *
     * @return array
     */
    protected function getGroupDefinition(\EWW\Dpf\Domain\Model\MetadataGroup $metadataGroup) {

        $formGroup = array();
        $formGroup['type'] = "Group";
        $formGroup['identifier'] = $metadataGroup->getUID();
        $formGroup['name'] = $metadataGroup->getName();
        $formGroup['label'] = $metadataGroup->getDisplayName();
        $formGroup['permission'] = $this->getPermision($metadataGroup->getBackendOnly());
        $formGroup['required'] = $metadataGroup->getMandatory();
        $formGroup['infoText'] = $metadataGroup->getInfoText();
        $formGroup['minOccurs'] = 1;
        $formGroup['maxOccurs'] = $this->getMaxOccurs($metadataGroup->getMaxIteration());
        $formGroup['mapping'] = $metadataGroup->getMapping();
        $formGroup['mappingForReading'] = $metadataGroup->getMappingForReading();
        $formGroup['modsExtensionMapping'] = $metadataGroup->getModsExtensionMapping();
        $formGroup['modsExtensionReference'] = $metadataGroup->getmodsExtensionReference();

        return $formGroup;
    }


    /**
     * Reads the definition of a form field (oblect)
     *
     * @param \EWW\Dpf\Domain\Model\MetadataObject $metadataObject
     *
     * @return array
     */
    protected function getFieldDefinition(\EWW\Dpf\Domain\Model\MetadataObject $metadataObject) {

        $formField = array();

        if (!$metadataObject->getConsent()) {
            switch ($metadataObject->getInputField()) {
                case $metadataObject::input:
                    $formField['type'] = "Text";
                    break;
                case $metadataObject::textarea:
                    $formField['type'] = "Textarea";
                    break;
                case $metadataObject::select:
                    $formField['type'] = "Select";
                    break;
                case $metadataObject::checkbox:
                    $formField['type'] = "Checkbox";
                    break;
                case 4:
                    $formField['type'] = "Hidden";
                    break;
                default:
                    $formField['type'] = "Text";
                    break;
            }
        } else {
            $formField['type'] = "ConsentCheckbox";
        }

        $formField['identifier'] = $metadataObject->getUID();
        $formField['name'] = $metadataObject->getName();
        $formField['label'] = $metadataObject->getDisplayName();
        $formField['permission'] = $this->getPermision($metadataObject->getBackendOnly());
        $formField['required'] = $metadataObject->getMandatory();
        $formField['minOccurs'] = 1;
        $formField['maxOccurs'] = $this->getMaxOccurs($metadataObject->getMaxIteration());

        // todo: inputOptionList
        // $formField['properties']['options'][] = array('value' => '', 'label' => '');

        $formField['fillOutService']['identifier'] = $metadataObject->getFillOutService();

        $formField['defaultValue'] = $metadataObject->getDefaultValue();

        $formField['validators'] = array();
        if ($metadataObject->getMandatory()) {
            $formField['validators'][] = array('identifier' => "NotEmpty");
        }

        switch ($metadataObject->getDataType()) {
            case "REGEXP":
                $formField['validators'][] = array('identifier' => "RegExp");
                break;

            case "DATA":
                $formField['validators'][] = array('identifier' => "Date");
                break;
        }

        $formField['mapping'] = $metadataObject->getMapping();
        $formField['modsExtension'] = $metadataObject->getModsExtension();

        return $formField;
    }


    /**
     * Determines a permission group based on the given backend only flag
     *
     * @param boolean $backendOnly
     *
     * @return string
     */
    protected function getPermision($backendOnly)
    {
        if ($backendOnly) {
            return "backend_only";
        } else {
            return "everybody";
        }
    }

    /**
     * Determines a value for maxOccurs based on maxIteration
     *
     * @param $maxIteration
     *
     * @return integer
     */
    protected function getMaxOccurs($maxIteration)
    {
        if ($maxIteration > 0) {
            return $maxIteration;
        } else {
            return 99;
        }
    }

}