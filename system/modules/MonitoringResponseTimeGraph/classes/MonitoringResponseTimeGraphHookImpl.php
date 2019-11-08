<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2019 Leo Feyer
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
 * @copyright  Cliff Parnitzky 2017-2019
 * @author     Cliff Parnitzky
 * @package    MonitoringResponseTimeGraph
 * @license    LGPL
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Monitoring;

/**
 * Class MonitoringResponseTimeGraphHookImpl
 *
 * Implementation of hooks.
 * @copyright  Cliff Parnitzky 2017-2019
 * @author     Cliff Parnitzky
 * @package    Controller
 */
class MonitoringResponseTimeGraphHookImpl extends \Backend
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Modify the header ... add the response time graph
   * @param  $arrHeaderFields  the headerfields given from list->sorting
   * @param  DataContainer $dc a DataContainer Object
   * @return Array The manipulated headerfields
   */
  public function addResponseTimeGraphToHeader($arrHeaderFields, \DataContainer $dc)
  {
    $monitoringEntryId = (int) $dc->id;
    
    // Make sure the dcaconfig.php file is loaded
    @include TL_ROOT . '/system/config/vis.js.php';
    
    $GLOBALS['TL_CSS'][] = 'assets/vis.js/' . VIS_JS . '/vis.min.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'assets/vis.js/' . VIS_JS . '/vis.min.js';
    
    $GLOBALS['TL_CSS'][] = 'system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph.min.css';
    
    $GLOBALS['TL_CSS'][] = 'system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph-menu.min.css';
    $GLOBALS['TL_MOOTOOLS'][] = '<script src="system/modules/MonitoringResponseTimeGraph/assets/responseTimeGraph-menu.min.js"></script>';
    
    $arrGroups = array();
    $strData = "";
    
    $objMonitoringTest = \MonitoringTestModel::findByPid($monitoringEntryId, array('order' => "date"));
    if ($objMonitoringTest !== null)
    {
      $responseTimeMin = 0;
      $responseTimeMax = 0;
      $responseTimeSum = 0;
      $responseTimeCount = 0;
      
      $firstDate = 0;
      $lastDate = 0;
      
      $hasValidResponseTimes = false;
      
      while($objMonitoringTest->next())
      {
        if ($objMonitoringTest->response_time > 0.0)
        {
          $hasValidResponseTimes = true;
          
          $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $objMonitoringTest->date) . ", "
                          . (date('m', $objMonitoringTest->date) - 1) . ", "
                          . date('d', $objMonitoringTest->date) . ", "
                          . date('H', $objMonitoringTest->date) . ", "
                          . date('i', $objMonitoringTest->date) . ", "
                          . date('s', $objMonitoringTest->date)
          . "), 'y': '" . $objMonitoringTest->response_time . "', 'group': 'org', 'label': {'content': '" . sprintf($GLOBALS['TL_LANG']['tl_monitoring_test']['response_time_format'], $objMonitoringTest->response_time) . "'}},";
          
          // collect dates for min, max and average
          if ($responseTimeCount == 0)
          {
            $firstDate = $objMonitoringTest->date;
          }
          $lastDate = $objMonitoringTest->date;
          
          // collect data for min, max and average
          if ($responseTimeCount == 0)
          {
            $responseTimeMin = $objMonitoringTest->response_time;
          }
          elseif ($objMonitoringTest->response_time < $responseTimeMin)
          {
            $responseTimeMin = $objMonitoringTest->response_time;
          }
          if ($objMonitoringTest->response_time > $responseTimeMax)
          {
            $responseTimeMax = $objMonitoringTest->response_time;
          }
          $responseTimeSum += $objMonitoringTest->response_time;
          $responseTimeCount++;
        }
      }
    }
    
    if ($hasValidResponseTimes)
    {
      // add data for min
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $firstDate) . ", "
                          . (date('m', $firstDate) - 1) . ", "
                          . date('d', $firstDate) . ", "
                          . date('H', $firstDate) . ", "
                          . date('i', $firstDate) . ", "
                          . date('s', $firstDate)
          . "), 'y': '" . $responseTimeMin . "', 'group': 'min'},";
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $lastDate) . ", "
                          . (date('m', $lastDate) - 1) . ", "
                          . date('d', $lastDate) . ", "
                          . date('H', $lastDate) . ", "
                          . date('i', $lastDate) . ", "
                          . date('s', $lastDate)
          . "), 'y': '" . $responseTimeMin . "', 'group': 'min'},";
      $responseTimeMinFormatted = sprintf($GLOBALS['TL_LANG']['tl_monitoring_test']['response_time_format'], $responseTimeMin);
      
      // add data for max
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $firstDate) . ", "
                          . (date('m', $firstDate) - 1) . ", "
                          . date('d', $firstDate) . ", "
                          . date('H', $firstDate) . ", "
                          . date('i', $firstDate) . ", "
                          . date('s', $firstDate)
          . "), 'y': '" . $responseTimeMax . "', 'group': 'max'},";
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $lastDate) . ", "
                          . (date('m', $lastDate) - 1) . ", "
                          . date('d', $lastDate) . ", "
                          . date('H', $lastDate) . ", "
                          . date('i', $lastDate) . ", "
                          . date('s', $lastDate)
          . "), 'y': '" . $responseTimeMax . "', 'group': 'max'},";
      $responseTimeMaxFormatted = sprintf($GLOBALS['TL_LANG']['tl_monitoring_test']['response_time_format'], $responseTimeMax);
      
      // add data for average
      $responseTimeAvg = round($responseTimeSum / $responseTimeCount, 3);
      
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $firstDate) . ", "
                          . (date('m', $firstDate) - 1) . ", "
                          . date('d', $firstDate) . ", "
                          . date('H', $firstDate) . ", "
                          . date('i', $firstDate) . ", "
                          . date('s', $firstDate)
          . "), 'y': '" . $responseTimeAvg . "', 'group': 'avg'},";
      $strData .= "{'x': new Date"
                        . "("
                          . date('Y', $lastDate) . ", "
                          . (date('m', $lastDate) - 1) . ", "
                          . date('d', $lastDate) . ", "
                          . date('H', $lastDate) . ", "
                          . date('i', $lastDate) . ", "
                          . date('s', $lastDate)
          . "), 'y': '" . $responseTimeAvg . "', 'group': 'avg'},";
      $responseTimeAvgFormatted = sprintf($GLOBALS['TL_LANG']['tl_monitoring_test']['response_time_format'], $responseTimeAvg);
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
    
    $arrHeaderFields[$GLOBALS['TL_LANG']['tl_monitoring']['responseTimeGraph'][0]] = <<<EOT

<style type="text/css">
  #monitoring-responseTimeGraph-legend {
    width: 98%;
    margin: 0 auto;
  }
  
  .vis-point {
    stroke-width:2px;
    fill-opacity:1.0;
  }

  .vis-legend-background {
    stroke-width:1px;
    fill-opacity:0.9;
    fill: #ffffff;
    stroke: #c2c2c2;
  }

  .vis-outline {
    stroke-width:1px;
    fill-opacity:1;
    fill: #ffffff;
    stroke: #e5e5e5;
  }

  .vis-icon-fill {
    fill-opacity:0.3;
    stroke: none;
  }

  div.description-container {
    float:left;
    height:15px;
    width:135px;
    padding-left:5px;
    line-height: 15px;
    overflow: hidden;
  }

  div.icon-container {
    float:left;
  }

  div.legend-element-container {
    display:inline-block;
    width:155px;
    height:15px;
    border-style:solid;
    border-width:1px;
    border-color: #e0e0e0;
    background-color: #d3e6ff;
    margin:4px;
    padding:4px;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    cursor:pointer;
  }
  div.legend-element-container.hidden {
    background-color: #ffffff;
  }

  svg.legend-icon {
    width:15px;
    height:15px;
  }
  
  text {
    display: none;
    cursor: default;
  }
  
  circle:hover + text {
    display: block;
  }

</style>

<div id="monitoring-responseTimeGraph">
  <div id="monitoring-responseTimeGraph-menu">
    <img id="zoomInResponseTimeGraph" src="system/modules/MonitoringResponseTimeGraph/assets/zoom-in.png" alt="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['zoom-in']}" title="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['zoom-in']}" />
    <img id="zoomOutResponseTimeGraph" src="system/modules/MonitoringResponseTimeGraph/assets/zoom-out.png" alt="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['zoom-out']}" title="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['zoom-out']}" />
    <img id="moveLeftResponseTimeGraph" src="system/modules/MonitoringResponseTimeGraph/assets/move-left.png" alt="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['move-left']}" title="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['move-left']}" />
    <img id="moveRightResponseTimeGraph" src="system/modules/MonitoringResponseTimeGraph/assets/move-right.png" alt="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['move-right']}" title="{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['menu']['move-right']}" />
  </div>
</div>
<div id="monitoring-responseTimeGraph-legend"></div>

<script type="text/javascript">
  // create a dataSet with groups
  var groups = new vis.DataSet();
  groups.add(
  {
    id: 'org',
    content: '{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['legend']['org']}',
    options: {
      drawPoints: {
        style: 'circle'
      }
    }
  });
  groups.add(
  {
    id: 'min',
  content: '{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['legend']['min']}: {$responseTimeMinFormatted}',
    options: {
      drawPoints: {
        enabled: false
      }
    }
  });
  groups.add(
  {
    id: 'max',
    content: '{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['legend']['max']}: {$responseTimeMaxFormatted}',
    options: {
      drawPoints: {
         enabled: false
      }
    }
  });
  groups.add(
  {
    id: 'avg',
  content: '{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['legend']['avg']}: {$responseTimeAvgFormatted}',
    options: {
      drawPoints: {
        enabled: false
      }
    }
  });

  // create data
  // note that months are zero-based in the JavaScript Date object
  var data = new vis.DataSet([{$strData}]);

  // specify options
  var options = {
    drawPoints: {style: 'circle', size: 4},
    dataAxis: {left: {range: {min: 0}, title: {text: '{$GLOBALS['TL_LANG']['MSC']['MonitoringResponseTimeGraph']['y_axis_label']}'}}},
    height: '200px',
    start: new Date({$startDateYear}, {$startDateMonth}, {$startDateDay}, 0, 0, 0),
    end: new Date({$endDateYear}, {$endDateMonth}, {$endDateDay}, 0, 0, 0)
  };

  // create the graph
  var container = document.getElementById('monitoring-responseTimeGraph');
  responseTimeGraph = new vis.Graph2d(container, data, groups, options);
  
  /**
   * this function fills the external legend with content using the getLegend() function.
   */
  function populateExternalLegend() {
    var groupsData = groups.get();
    var legendDiv = document.getElementById("monitoring-responseTimeGraph-legend");
    legendDiv.innerHTML = "";

    // get for all groups:
    for (var i = 0; i < groupsData.length; i++) {
      // create divs
      var containerDiv = document.createElement("div");
      var iconDiv = document.createElement("div");
      var descriptionDiv = document.createElement("div");

      // give divs classes and Ids where necessary
      containerDiv.className = 'legend-element-container';
      containerDiv.id = groupsData[i].id + "_legendContainer";
      iconDiv.className = "icon-container";
      descriptionDiv.className = "description-container";
      

      // get the legend for this group.
      var legend = responseTimeGraph.getLegend(groupsData[i].id,15,15);
      
      // set title attribute to support tooltips
      containerDiv.title = legend.label;
      
      // append class to icon. All styling classes from the vis.css/vis-timeline-graph2d.min.css have been copied over into the head here to be able to style the
      // icons with the same classes if they are using the default ones.
      legend.icon.setAttributeNS(null, "class", "legend-icon");

      // append the legend to the corresponding divs
      iconDiv.appendChild(legend.icon);
      descriptionDiv.innerHTML = legend.label;

      // determine the order for left and right orientation
      if (legend.orientation == 'left') {
        descriptionDiv.style.textAlign = "left";
        containerDiv.appendChild(iconDiv);
        containerDiv.appendChild(descriptionDiv);
      }
      else {
        descriptionDiv.style.textAlign = "right";
        containerDiv.appendChild(descriptionDiv);
        containerDiv.appendChild(iconDiv);
      }

      // append to the legend container div
      legendDiv.appendChild(containerDiv);

      // bind click event to this legend element.
      containerDiv.onclick = toggleGraph.bind(this,groupsData[i].id);
    }
  }

  /**
   * This function switchs the visible option of the selected group on an off.
   * @param groupId
   */
  function toggleGraph(groupId) {
    // get the container that was clicked on.
    var container = document.getElementById(groupId + "_legendContainer")
    // if visible, hide
    if (responseTimeGraph.isGroupVisible(groupId) == true) {
      groups.update({id:groupId, visible:false});
      container.className = container.className + " hidden";
    }
    else { // if invisible, show
      groups.update({id:groupId, visible:true});
      container.className = container.className.replace("hidden","");
    }
  }

  populateExternalLegend();

</script>
EOT;

    return $arrHeaderFields;
  }
}

?>