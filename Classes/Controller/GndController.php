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
 * GndController
 */
class GndController extends \EWW\Dpf\Controller\AbstractController
{
    // TODO: Make gndHost and Urls configurable
    protected $gndHost = 'https://xgnd.bsz-bw.de/';

    protected $searchUrl = 'Anfrage?suchfeld=pica.swr&suchfilter=000000&suchoptionen=pica.tbs%3D%22s%22&suchwort=';

    protected $gndResolverUrl = 'Anfrage?suchfeld=pica.ppn&suchoptionen=pica.tbs%3D"s"+and+&suchwort=';

    protected $baseGndValueUri = 'http://d-nb.info/gnd/';

    /**
     * @param string $search
     * @return array
     */
    public function searchAction($search) {

        $url = $this->gndHost . $this->searchUrl . $search;
        $content = file_get_contents($url);
        $json = json_decode($content);

        $listArray = array();
        $i = 0;
        foreach ($json as $value) {
            $listArray[$i]['value'] = $value->Ansetzung;
            $listArray[$i]['ppn'] = $value->PPN;
            $listArray[$i]['typ'] = $value->Typ;
            if ($value->GNDNr) {
                $listArray[$i]['gnd'] = $this->baseGndValueUri.$value->GNDNr;
            } else {
                $listArray[$i]['gnd'] = $this->baseGndValueUri.$this->gndAction($value->PPN);
            }
            $i++;
        }

        if (empty($listArray)) {
            echo json_encode(['value' => 'Keine Treffer gefunden!']);
        } else {
            echo json_encode($listArray);
        }

        return '';
    }

    /**
     * @param string $PPN
     * @return mixed
     */
    public function gndAction($PPN) {

        $url = $this->gndHost . $this->gndResolverUrl . $PPN;
        $content = file_get_contents($url);
        $json = json_decode($content);

        return $json[0]->GNDNr;
    }
}