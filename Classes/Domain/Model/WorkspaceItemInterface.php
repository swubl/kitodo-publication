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

interface WorkspaceItemInterface
{
    /**
     * @return string
     */
    public function getClassShortName();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return array
     */
    public function getAuthors();

    /**
     * @return \EWW\Dpf\Domain\Model\DocumentType
     */
    public function getDocumentType();

    /**
     * @return string
     */
    public function getObjectIdentifier();

    /**
     * @return string
     */
    public function getRemoteStatus();

    /**
     * @return string
     */
    public function getDateIssued();

    /**
     * @return integer
     */
    public function getOwner();

}