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

        if ($this->doc === null || empty($this->conf['fileGrpDownload'])) {
            // Quit without doing anything if required variables are not set.
           return $content;
        }

        // Load template file.
        if (!empty($this->conf['templateFile'])) {

            $this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

        } else {

            $this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dpf/Classes/Plugins/StatisticTool/template.tmpl'), '###TEMPLATE###');

        }

        $subpartArray['statistic'] = $this->cObj->getSubpart($this->template, '###STATISTIC###');

        // get statistic data
        $conf = array(
            'useCacheHash'     => 0,
            'parameter'        => $this->conf['apiPid'] . ' - piwik_download',
            'additionalParams' => '&tx_dpf[qid]=' . $this->doc->recordId . '&tx_dpf[action]=attachment' . '&tx_dpf[attachment]=STAT-0',
            'forceAbsoluteUrl' => true,
        );

        $statisticData = file( $this->cObj->typoLink_URL($conf), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!is_array($statisticData) || !sizeof($statisticData) > 0 || !preg_match("/^[0-9]{2}-[0-9]{4}/",$statisticData[0],$matches)) {
            return $content;
        }

        $statData = array();
        foreach ($statisticData as $item) {
            list($monthYear, $object, $count) = explode(' ', $item);
            // list($month, $year) = explode('-',trim($monthYear));
            //$statData[trim(intval($year))][trim(intval($month))][trim(strtolower($object))] = trim(intval($count));
            $statData[$monthYear][trim(strtolower($object))] = trim(intval($count));
        }

        $statData = array_slice($statData,0,12);
        foreach ($statData as $key => $value) {
            list($month, $year) = explode('-',trim($key));
            $data[trim(intval($year))][trim(intval($month))] = $value;
        }

        if (is_array($data) && $data) {
            $content = '';
            $markerArray['###DATA###'] = '<script>var ar = '.json_encode($data).'</script>';
            $content .= $this->cObj->substituteMarkerArray($subpartArray['statistic'], $markerArray);
            return $this->cObj->substituteSubpart($this->template, '###STATISTIC###', $content, true);
        }

        return $content;

    }
}
