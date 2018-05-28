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


use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BkController
 */
class BkController extends \EWW\Dpf\Controller\AbstractController
{
    protected $searchUrl = 'https://uri.gbv.de/terminology/bk/';


    /**
     * @param string $search
     * @return string
     */
    public function searchAction($search) {
        $url = $this->gndHost . $this->searchUrl . $search . "?format=jsonld";
        $content = file_get_contents($url);
        $json = json_decode($content);

        if (is_object($json)) {
            if ($json->prefLabel) {
                if ($json->prefLabel->de)
                    echo $search ." ". $json->prefLabel->de;
            }
        }
        return '';
    }
}