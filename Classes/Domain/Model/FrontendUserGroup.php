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
* UserGroup
*/
class FrontendUserGroup extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
{
    /**
     * @var int
     */
    protected $kitodoRole = 0;

    public function getKitodoRole()
    {
        return $this->kitodoRole;
    }

    public function setKitodoRole($kitodoRole)
    {
        $this->kitodoRole = $kitodoRole;
    }

}