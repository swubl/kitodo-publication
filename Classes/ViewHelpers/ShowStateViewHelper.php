<?php
namespace EWW\Dpf\ViewHelpers;

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

class ShowStateViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     *
     * @param string $state
     *
     */
    public function render($state)
    {

        $key = "";

        switch ($state) {
            case \EWW\Dpf\Domain\Model\Document::OBJECT_STATE_NEW:
                $key = 'search.resultList.state.new';
                break;
            case \EWW\Dpf\Domain\Model\Document::OBJECT_STATE_ACTIVE:
            case 'A':
                $key = 'search.resultList.state.active';
                break;
            case \EWW\Dpf\Domain\Model\Document::OBJECT_STATE_INACTIVE:
            case 'I':
                $key = 'search.resultList.state.inactive';
                break;
            case \EWW\Dpf\Domain\Model\Document::OBJECT_STATE_DELETED:
            case \EWW\Dpf\Domain\Model\Document::OBJECT_STATE_LOCALLY_DELETED:
            case 'D':
                $key = 'search.resultList.state.deleted';
                break;
            default:
                return "-";
                break;
        }

        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'dpf', $arguments = null);
    }
}
