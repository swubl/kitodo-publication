<?php
namespace EWW\Dpf\Controller;

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

use EWW\Dpf\Domain\Model\Document;
use EWW\Dpf\Helper\DocumentMapper;
use EWW\Dpf\Exceptions\AccessDeniedExcepion;
use EWW\Dpf\Security\DocumentVoter;
use EWW\Dpf\Security\Security;
use EWW\Dpf\Services\Transfer\ElasticsearchRepository;
use EWW\Dpf\Exceptions\DPFExceptionInterface;
use EWW\Dpf\Domain\Workflow\DocumentWorkflow;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class DocumentFormBackofficeController extends AbstractDocumentFormController
{

    public function __construct()
    {
        parent::__construct();

    }

    protected function redirectToCurrentWorkspace($message = null)
    {
        $redirectAction = $this->getSessionData('currentWorkspaceAction');

        $redirectAction = empty($redirectAction)? 'defaultAction' : $redirectAction;

        $this->redirect($redirectAction, 'Document', null, array('message' => $message));
    }

    protected function redirectToList($message = null)
    {
        $this->redirect('list', 'DocumentFormBackoffice', null);
    }

    /**
     * action delete
     *
     * @param array $documentData
     * @throws \Exception
     */
    public function deleteAction($documentData)
    {
        /* @var $document \EWW\Dpf\Domain\Model\Document */
        $document = $this->documentRepository->findByUid($documentData['documentUid']);
        $this->authorizationChecker->denyAccessUnlessGranted(DocumentVoter::DELETE, $document);

        try {
            $elasticsearchRepository = $this->objectManager->get(ElasticsearchRepository::class);
            // send document to index
            $elasticsearchRepository->delete($document, "");

            //$document->setLocalStatus(LocalDocumentStatus::DELETED);

            $this->documentRepository->update($document);

            $this->redirectToList();

        } catch (\Exception $exception) {

            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;

            if ($exception instanceof DPFExceptionInterface) {
                $key = $exception->messageLanguageKey();
            } else {
                $key = 'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:error.unexpected';
            }

            $document = $this->documentRepository->findByUid($documentData['documentUid']);
            $message[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:document_delete.failure',
                'dpf',
                array($document->getTitle())
            );

            $message[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'dpf');
            $this->addFlashMessage(implode(" ", $message), '', $severity,true);

            $this->forward('edit', 'DocumentFormBackoffice', null, array('document' => $document));
        }


    }

    public function arrayRecursiveDiff($aArray1, $aArray2) {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }
        return $aReturn;
    }


    /**
     * action edit
     *
     * @param \EWW\Dpf\Domain\Model\DocumentForm $documentForm
     * @param bool $suggestMod
     * @ignorevalidation $documentForm
     * @return void
     */
    public function editAction(\EWW\Dpf\Domain\Model\DocumentForm $documentForm, bool $suggestMod = false)
    {
        $document = $this->documentRepository->findByUid($documentForm->getDocumentUid());
        $this->view->assign('document', $document);
        $this->view->assign('suggestMod', $suggestMod);
        $this->authorizationChecker->denyAccessUnlessGranted(DocumentVoter::EDIT, $document);

        parent::editAction($documentForm);
    }

    /**
     * @param \EWW\Dpf\Domain\Model\DocumentForm $documentForm
     */
    public function createSuggestionDocumentAction(\EWW\Dpf\Domain\Model\DocumentForm $documentForm) {

        $documentMapper = $this->objectManager->get(DocumentMapper::class);

        /* @var $newDocument \EWW\Dpf\Domain\Model\Document */
        $document = $documentMapper->getDocument($documentForm);

        $newDocument = $this->objectManager->get(Document::class);

        $this->documentRepository->add($newDocument);
        $this->persistenceManager->persistAll();

        $newDocument = $newDocument->copy($document);
        $newDocument->setLinkedUid($document->getUid());
        $newDocument->setSuggestion(true);

        // remove files
        $newDocument->setFile($this->objectManager->get(ObjectStorage::class));

        try {
            $this->documentRepository->add($newDocument);
            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::SUCCESS;
            $this->addFlashMessage("Success", '', $severity,false);
        } catch (\Throwable $t) {
            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;
            $this->addFlashMessage("Failed", '', $severity,false);
        }

        $this->redirectToCurrentWorkspace();

    }

    public function updateAction(\EWW\Dpf\Domain\Model\DocumentForm $documentForm)
    {
        if ($this->request->getArgument('documentData')['suggestMod']) {
            $this->forward('createSuggestionDocument', null, null, ['documentForm' => $documentForm]);
        }

        $document = $this->documentRepository->findByUid($documentForm->getDocumentUid());
        $this->view->assign('document', $document);
        $this->authorizationChecker->denyAccessUnlessGranted(DocumentVoter::UPDATE, $document);

        try {
            parent::updateAction($documentForm);
        } catch (\Exception $exception) {

            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;

            if ($exception instanceof DPFExceptionInterface) {
                $key = $exception->messageLanguageKey();
            } else {
                $key = 'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:error.unexpected';
            }

            $documentMapper = $this->objectManager->get(\EWW\Dpf\Helper\DocumentMapper::class);
            $updateDocument = $documentMapper->getDocument($documentForm);

            if ($this->security->getUserRole() === Security::ROLE_LIBRARIAN &&
                $updateDocument->getState() !== DocumentWorkflow::STATE_NEW_NONE) {

                $state = explode(":", $updateDocument->getState());

                $state[0] = DocumentWorkflow::LOCAL_STATE_IN_PROGRESS;
                $updateDocument->setState(implode(":", $state));

            }

            $message[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:document_update.failure',
                'dpf',
                array($updateDocument->getTitle())
            );

            $message[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'dpf');


            $this->addFlashMessage(implode(" ", $message), '', $severity,true);

            $this->forward('edit', 'DocumentFormBackoffice', null, array('document' => $updateDocument));
        }
    }

    public function createAction(\EWW\Dpf\Domain\Model\DocumentForm $newDocumentForm)
    {
        $documentMapper = $this->objectManager->get(DocumentMapper::class);
        $document = $documentMapper->getDocument($newDocumentForm);
        $this->authorizationChecker->denyAccessUnlessGranted(DocumentVoter::CREATE, $document);

        try {
            parent::createAction($newDocumentForm);

            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK;
            $key = 'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:documentForm.create.ok';
            $message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'dpf', $args);
            $this->addFlashMessage(
                $message,
                '',
                $severity,
                true
            );
        } catch (\Exception $exception) {

            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;

            if ($exception instanceof DPFExceptionInterface) {
                $key = $exception->messageLanguageKey();
            } else {
                $key = 'LLL:EXT:dpf/Resources/Private/Language/locallang.xlf:error.unexpected';
            }

            $message[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'dpf');

            $this->addFlashMessage(
                implode(" ", $message),
                '',
                $severity,
                true
            );
        }

        $this->redirectToList();
    }

    public function initializeAction()
    {
        $this->authorizationChecker->denyAccessUnlessLoggedIn();

        parent::initializeAction();

    }

}
