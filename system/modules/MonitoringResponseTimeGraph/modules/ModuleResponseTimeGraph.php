<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2017 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Cliff Parnitzky 2017-2017
 * @author     Cliff Parnitzky
 * @package    MonitoringResponseTimeGraph
 * @license    LGPL
 */

namespace Monitoring;


/**
 * Back end module "Response times".
 *
 * @author     Cliff Parnitzky
 */
class ModuleResponseTimeGraph extends \BackendModule
{

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'monitoring_responseTimeGraph';


  /**
   * Generate the module
   */
  protected function compile()
  {
    \System::loadLanguageFile('tl_monitoring_responseTimeGraph');
    \System::loadLanguageFile('tl_monitoring_test');
    
    // Make sure the dcaconfig.php file is loaded
    @include TL_ROOT . '/system/config/vis.js.php';
    
    $GLOBALS['TL_CSS'][] = 'assets/vis.js/' . VIS_JS . '/vis.min.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'assets/vis.js/' . VIS_JS . '/vis.min.js';
    
    $GLOBALS['TL_CSS'][] = 'system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph.min.css';
    
    $GLOBALS['TL_CSS'][] = 'system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph-menu.min.css';
    $GLOBALS['TL_MOOTOOLS'][] = '<script src="system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph-menu.min.js"></script>';
    
    $arrGroups = array();
    $strData = "";
    
    $objMonitoringEntry = \MonitoringModel::findAllActive();
    if ($objMonitoringEntry !== null)
    {
      while ($objMonitoringEntry->next())
      {
        $arrGroups[$objMonitoringEntry->id] = array('customer' => $objMonitoringEntry->customer, 'website' => $objMonitoringEntry->website);
        
        $objMonitoringTest = \MonitoringTestModel::findByPid($objMonitoringEntry->id, array('order' => "date"));
        if ($objMonitoringTest !== null)
        {
          while($objMonitoringTest->next())
          {
            if ($objMonitoringTest->response_time > 0.0)
            {
              $strData .= "{'x': new Date"
                            . "("
                              . date('Y', $objMonitoringTest->date) . ", "
                              . (date('m', $objMonitoringTest->date) - 1) . ", "
                              . date('d', $objMonitoringTest->date) . ", "
                              . date('H', $objMonitoringTest->date) . ", "
                              . date('i', $objMonitoringTest->date) . ", "
                              . date('s', $objMonitoringTest->date)
                            . "), 'y': '" . $objMonitoringTest->response_time . "', 'group': " . $objMonitoringEntry->id . ", 'label': {'content': '" . sprintf($GLOBALS['TL_LANG']['tl_monitoring_test']['response_time_format'], $objMonitoringTest->response_time) . "'}},";
            }
          }
        }
      }
    }
    
    $today = time();
    
    $startDate = mktime(0, 0, 0, date("m", $today)  , date("d", $today) - 30, date("Y", $today));
    $startDateDay = date("d", $startDate);
    $startDateMonth = date("m", $startDate) - 1; // note that months are zero-based in the JavaScript Date object
    $startDateYear = date("Y", $startDate);
    
    $endDate = mktime(0, 0, 0, date("m", $today)  , date("d", $today) + 2, date("Y", $today));
    $endDateDay = date("d", $endDate);
    $endDateMonth = date("m", $endDate) - 1; // note that months are zero-based in the JavaScript Date object
    $endDateYear = date("Y", $endDate);
    
    $this->Template->chartGroups = $arrGroups;
    $this->Template->chartData = $strData;
    
    $this->Template->startDateDay = $startDateDay;
    $this->Template->startDateMonth = $startDateMonth;
    $this->Template->startDateYear = $startDateYear;

    $this->Template->endDateDay = $endDateDay;
    $this->Template->endDateMonth = $endDateMonth;
    $this->Template->endDateYear = $endDateYear;
    
    $this->Template->href = $this->getReferer(true);
    $this->Template->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
    $this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
    $this->Template->headline = $GLOBALS['TL_LANG']['tl_monitoring_responseTimeGraph']['headline'];
  }
}

?>