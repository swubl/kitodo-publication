<?php
namespace EWW\Dpf\Security;

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


class LibrarianAuthorization extends Authorization
{
    /**
     *
     * @param string $arttribute
     */
    public function checkAttributePermission($attribute)
    {
        switch ($attribute) {
            case 'EWW\Dpf\Controller\DocumentController::listAction':
            case 'EWW\Dpf\Controller\DocumentController::listNewAction':
            case 'EWW\Dpf\Controller\DocumentController::listEditAction':
            case 'EWW\Dpf\Controller\DocumentController::discardAction':
            case 'EWW\Dpf\Controller\DocumentController::duplicateAction':
            case 'EWW\Dpf\Controller\DocumentFormBackofficeController::editAction':
            case 'EWW\Dpf\Controller\DocumentFormBackofficeController::updateAction':
            case 'EWW\Dpf\Controller\SearchController::doubletCheckAction':
            case 'EWW\Dpf\Controller\SearchController::listAction':
            case 'EWW\Dpf\Controller\SearchController::searchAction':
            case 'EWW\Dpf\Controller\SearchController::extendedSearchAction':
            case 'EWW\Dpf\Controller\SearchController::latestAction': {
                return TRUE;
                break;
            }

            default: return FALSE;
        }
    }
}