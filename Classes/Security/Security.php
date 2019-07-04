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

class Security
{
    public static function isAllowed($backofficeOnly, $role) {

        if ($backofficeOnly) {
            return
                //((TYPO3_MODE === 'BE') && \EWW\Dpf\Security\Security::hasBackendRole($role))
                (TYPO3_MODE === 'BE')
                || \EWW\Dpf\Security\Security::hasFrontendRole($role);
        } else {
            return TRUE;
        }
    }

    public static function hasFrontendRole($role = NULL) {

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $frontendUserGroupRepository = $objectManager->get(\EWW\Dpf\Domain\Repository\FrontendUserGroupRepository::class);

        $kitotoRoleIds = array();
        if (is_array($GLOBALS['TSFE']->fe_user->groupData['uid'])) {
            foreach ($GLOBALS['TSFE']->fe_user->groupData['uid'] as $groupUid) {
                $group = $frontendUserGroupRepository->findByUid($groupUid);
                if (is_object($group)) {
                    $kitotoRoleIds[] = $group->getKitodoRole();
                }
            }
        }
        
        // Taken from viewhelper: IfHasRoleViewHelper
        // typo2/sysext/fluid/Classes/ViewHelpers/Security/IfHasRoleViewhelper.php
        if (!isset($GLOBALS['TSFE']) || !$GLOBALS['TSFE']->loginUser) {
            return false;
        }
        if (is_numeric($role)) {
            return in_array($role, $kitotoRoleIds);
        } else {
            return false;
            //return is_array($GLOBALS['TSFE']->fe_user->groupData['title']) && in_array($role, $GLOBALS['TSFE']->fe_user->groupData['title']);
        }
    }

    public static function hasBackendRole($role = NULL) {
        // Taken from viewhelper: IfHasRoleViewHelper
        // typo2/sysext/fluid/Classes/ViewHelpers/Be/Security/IfHasRoleViewhelper.php
        if (!is_array($GLOBALS['BE_USER']->userGroups)) {
            return false;
        }
        if (is_numeric($role)) {
            foreach ($GLOBALS['BE_USER']->userGroups as $userGroup) {
                if ((int)$userGroup['uid'] === (int)$role) {
                    return true;
                }
            }
        } else {
            foreach ($GLOBALS['BE_USER']->userGroups as $userGroup) {
                if ($userGroup['title'] === $role) {
                    return true;
                }
            }
        }
        return false;

    }

}