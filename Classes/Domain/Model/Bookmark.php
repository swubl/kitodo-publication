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

use EWW\Dpf\Domain\Model\RemoteDocumentStatus;


/**
 * Document
 */
class Document extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * authors
     *
     * @var string
     */
    protected $authors = '';

    /**
     * documentType
     *
     * @var \EWW\Dpf\Domain\Model\DocumentType
     */
    protected $documentType = null;

    /**
     * objectIdentifier
     *
     * @var string
     */
    protected $objectIdentifier;

    /**
     * remoteStatus
     *
     * @var string
     */
    protected $remoteStatus = NULL;

    /**
     *
     * @var string $dateIssued
     */
    protected $dateIssued;

    /**
     * owner
     *
     * @var integer
     */
    protected $owner = 0;


    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the authors
     *
     * @return array $authors
     */
    public function getAuthors()
    {
        return array_map('trim', explode(";", $this->authors));
    }

    /**
     * Sets the authors
     *
     * @param array $authors
     * @return void
     */
    public function setAuthors($authors)
    {
        $authors       = implode("; ", $authors);
        $this->authors = $authors;
    }

    /**
     * Returns the documentType
     *
     * @return \EWW\Dpf\Domain\Model\DocumentType $documentType
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * Sets the documentType
     *
     * @param \EWW\Dpf\Domain\Model\DocumentType $documentType
     * @return void
     */
    public function setDocumentType(\EWW\Dpf\Domain\Model\DocumentType $documentType)
    {
        $this->documentType = $documentType;
    }

    /**
     * Returns the objectIdentifier
     *
     * @return string
     */
    public function getObjectIdentifier()
    {
        return $this->objectIdentifier;
    }

    /**
     * Sets the objectIdentifier
     *
     * @param string $objectIdentifier
     * @return void
     */
    public function setObjectIdentifier($objectIdentifier)
    {
        $this->objectIdentifier = $objectIdentifier;
    }

    /**
     * Returns the remoteStatus
     *
     * @return string
     */
    public function getRemoteStatus()
    {
        return $this->remoteStatus;
    }

    /**
     * Sets the remoteStatus
     *
     * @return string
     */
    public function setRemoteStatus($remoteStatus)
    {
        $this->remoteStatus = $remoteStatus;
    }

    /**
     * Gets the Issue Date
     *
     * @return string
     */
    public function getDateIssued()
    {
        return empty($this->dateIssued) ? '' : $this->dateIssued;
    }

    /**
     * Sets the Issue Date
     *
     * @param string $dateIssued
     * @return void
     */
    public function setDateIssued($dateIssued)
    {
        $this->dateIssued = empty($dateIssued) ? '' : $dateIssued;
    }

    /**
     * Returns the owner uid
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets the owner uid
     *
     * @param integer $owner
     * @return void
     */
    public function setOwner($owner)
    {
        $this->owner = owner;
    }

}
