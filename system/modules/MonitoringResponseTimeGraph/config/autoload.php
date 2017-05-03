<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package MonitoringResponseTimeGraph
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Monitoring',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Monitoring\MonitoringResponseTimeGraphHookImpl' => 'system/modules/MonitoringResponseTimeGraph/classes/MonitoringResponseTimeGraphHookImpl.php',
));
