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

class AuthorizationChecker
{
    /**
     * objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager = null;

    /**
     * frontendUserGroupRepository
     *
     * @var \EWW\Dpf\Domain\Repository\FrontendUserGroupRepository
     * @inject
     */
    protected $frontendUserGroupRepository = null;

    /**
     * frontendUserRepository
     *
     * @var \EWW\Dpf\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository = null;

    const ROLE_ANONYMOUS = "ROLE_ANONYMOUS";
    const ROLE_RESEARCHER = "ROLE_RESEARCHER";
    const ROLE_LIBRARIAN = "ROLE_LIBRARIAN";

    /**
     * @param string $attribute
     * @param string $plugin
     * @return bool
     */
    public function isGranted($attribute, $plugin = NULL) {

        $clientUserRoles = $this->getClientUserRoles();
        $clientUserRoles[] = self::ROLE_ANONYMOUS;

        foreach ($clientUserRoles as $role) {

            $roleAuthorization = $this->getAuthorizationByRole($role);
            if ($roleAuthorization->checkAttributePermission($attribute)) {
                return TRUE;
            } else {
                continue;
            }
        }

        return FALSE;

    }


    /**
     * Get the roles the user has in the current client
     *
     * @return array
     */
    public function getClientUserRoles() {

        // Get frontend user groups of the client.
        $clientFrontendGroups = array();
        foreach ($this->frontendUserGroupRepository->findAll() as $clientGroup) {
            $clientFrontendGroups[$clientGroup->getUid()] = $clientGroup;
        }

        // Get frontend user groups of the user.
        $frontendUserGroups = array();
        $frontendUser = $this->getUser();
        if ($frontendUser) {
            foreach ($frontendUser->getUsergroup() as $userGroup) {
                // Because getUsergroup() does not return objects of the class
                // \EWW\Dpf\Domain\Repository\FrontendUserRepository
                $userGroup = $this->frontendUserGroupRepository->findByUid($userGroup->getUid());
                $frontendUserGroups[$userGroup->getUid()] = $userGroup;
            }
        }

        // Get the roles the user has in the current client.
        $roles = array();
        foreach ($frontendUserGroups as $uid => $group) {
            if (array_key_exists($uid, $clientFrontendGroups)) {
                $roles[$uid] = $group->getKitodoRole();
            }
        }

        return $roles;

    }

    /**
     * Gets an authorization object associated with the given role
     *
     * @param string $role
     */
    protected function getAuthorizationByRole($role)
    {
        $authorizationClass = ucfirst(strtolower(str_replace('ROLE_','', $role))).'Authorization';
        $authorizationClass = 'EWW\\Dpf\\Security\\'.$authorizationClass;

        if (class_exists($authorizationClass)) {
            return $this->objectManager->get($authorizationClass);
        }

        return NULL;
    }

    /**
     * Gets the logged in user
     *
     * @return mixed
     */
    protected function getUser() {
        return $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
    }



}