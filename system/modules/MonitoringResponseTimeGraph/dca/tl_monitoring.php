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
 * Add css for styling global operations button
 */
$GLOBALS['TL_CSS'][] = 'system/modules/MonitoringResponseTimeGraph/assets/styles.css';

/**
 * Add global operations
 */
array_insert($GLOBALS['TL_DCA']['tl_monitoring']['list']['global_operations'], count($GLOBALS['TL_DCA']['tl_monitoring']['list']['global_operations']) - 1, array
(
    'filteredResponseTimeGraphs' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['tl_monitoring']['filteredResponseTimeGraphs'],
        'href'                => 'key=navigateToMonitoringResponseTimeGraph',
        'class'               => 'header_icon tl_monitoring_filteredResponseTimeGraphs',
        'attributes'          => 'onclick="Backend.getScrollOffset()"'
    )
));

?>