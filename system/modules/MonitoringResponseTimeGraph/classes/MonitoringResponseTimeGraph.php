<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2018 Leo Feyer
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
 * @copyright  Cliff Parnitzky 2018-2018
 * @author     Cliff Parnitzky
 * @package    MonitoringResponseTimeGraph
 * @license    LGPL
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Monitoring;

/**
 * Class MonitoringResponseTimeGraph
 *
 * Contains functions to be used with the response time components.
 * @copyright  Cliff Parnitzky 2018-2018
 * @author     Cliff Parnitzky
 * @package    Controller
 */
class MonitoringResponseTimeGraph extends \Backend
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Get all ids of the current filtered list of monitoring entries 
   */
  public function navigateToMonitoringResponseTimeGraph()
  {
    $arrFilter = \Session::getInstance()->get('filter')['tl_monitoring'];
    $arrSearch = \Session::getInstance()->get('search')['tl_monitoring'];
    unset($arrFilter['limit']);
    
    $select = "SELECT id FROM tl_monitoring";
    if (!empty($arrFilter) || !empty($arrSearch['value']))
    {
      $select .= " WHERE ";
      
      if (!empty($arrFilter))
      {
        $select .= implode(" = ? AND ", array_keys($arrFilter)) . " = ?";
      }
      
      if (!empty($arrSearch['value']))
      {
        if (!empty($arrFilter))
        {
          $select .= " AND ";
        }
        $select .= " " . $arrSearch['field'] . " LIKE ?";
        // add the value to the paramters array
        $arrFilter[$arrSearch['field']] = "%" . $arrSearch['value'] . "%";
      }
    }
    
    $objIds = \Database::getInstance()->prepare($select)
                                      ->execute(array_values($arrFilter));
    
    $arrIds = $objIds->numRows ? $objIds->fetchEach('id') : array();

    $path = \Environment::get('base') . 'contao/main.php?do=monitoringResponseTimeGraph';
    if (!empty($arrIds))
    {
      $path .= '&amp;ids=' . implode(',', $arrIds);
    }
    $this->redirect($path, 301);
  }

}

?>