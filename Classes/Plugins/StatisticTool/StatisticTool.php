<?php
namespace EWW\Dpf\Plugins\StatisticTool;

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
use TYPO3\CMS\Extbase\Exception;

/**
 * Plugin 'DPF: StatisticTool' for the 'dlf / dpf' extension.
 *
 * @package    TYPO3
 * @subpackage    tx_dpf
 * @access    public
 */
class StatisticTool extends \tx_dlf_plugin
{

    /**
     * The main method of the PlugIn
     *
     * @access    public
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     *
     * @return    string        The content that is displayed on the website
     */
    public function main($content, $conf)
    {

        $this->init($conf);

        // get the tx_dpf.settings too
        // Flexform wins over TS
        $dpfTSconfig = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_dpf.'];

        if (is_array($dpfTSconfig['settings.'])) {

            \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($dpfTSconfig['settings.'], $this->conf, true, false);
            $this->conf = $dpfTSconfig['settings.'];

        }

        // Load current document.
        $this->loadDocument();

//        if ($this->doc === null || empty($this->conf['fileGrpDownload'])) {

            // Quit without doing anything if required variables are not set.
//            return $content;

//        }

        // Load template file.
        if (!empty($this->conf['templateFile'])) {

            $this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

        } else {

            $this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dpf/Classes/Plugins/StatisticTool/template.tmpl'), '###TEMPLATE###');

        }





        $statisticData = $this->getStatisticData();

        $content = '';

        if (is_array($attachments)) {

            foreach ($attachments as $id => $file) {

                $conf = array(
                    'useCacheHash'     => 0,
                    'parameter'        => $this->conf['apiPid'] . ' - piwik_download',
                    'additionalParams' => '&tx_dpf[qid]=' . $this->doc->recordId . '&tx_dpf[action]=attachment' . '&tx_dpf[attachment]=' . $file['ID'],
                    'forceAbsoluteUrl' => true,
                );

                $title = $file['LABEL'] ? $file['LABEL'] : $file['ID'];

                // Create a-tag without VG-Wort Redirect
                if ($vgwort === FALSE) {

                    // replace uid with URI to dpf API
                    $markerArray['###FILE###'] = $this->cObj->typoLink($title, $conf);

                    // Create a-tag with VG-Wort Redirect
                } elseif(!empty($vgwort)) {

                    $qucosaUrl = urlencode($this->cObj->typoLink_URL($conf));

                    $confVgwort = array(
                        'useCacheHash'     => 0,
                        'parameter'        => $vgwort . $qucosaUrl . ' - piwik_download',
                    );

                    $markerArray['###FILE###'] = $this->cObj->typoLink($title, $confVgwort);

                }

                $content .= $this->cObj->substituteMarkerArray($subpartArray['downloads'], $markerArray);

            }

        }

        return $this->template;

    }

    /**
     * Get Statistic data

     * @return array
     */
    protected function getStatisticData()
    {

        $conf = array(
            'useCacheHash'     => 0,
            'parameter'        => $this->conf['apiPid'] . ' - piwik_download',
            'additionalParams' => '&tx_dpf[qid]=' . $this->doc->recordId . '&tx_dpf[action]=attachment' . '&tx_dpf[attachment]=STAT-0',
            'forceAbsoluteUrl' => true,
        );


        $statisticData = file( $this->cObj->typoLink_URL($conf), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // $statisticData = file_get_contents($this->cObj->typoLink_URL($conf));

        $data = array();
        foreach ($statisticData as $item) {
            list($monthYear, $object, $count) = explode(' ', $item);
            list($month, $year) = explode('-',trim($monthYear));
            $data[trim(intval($year))][trim(intval($month))][trim(strtolower($object))] = trim(intval($count));
        }

        //echo "<script>var ar = ".json_encode($data)."</script>";

        return $attachments;
    }

}
