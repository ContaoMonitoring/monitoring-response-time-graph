<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
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

	// Modules
	'Monitoring\ModuleResponseTimeGraph'             => 'system/modules/MonitoringResponseTimeGraph/modules/ModuleResponseTimeGraph.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'monitoring_responseTimeGraph' => 'system/modules/MonitoringResponseTimeGraph/templates',
));
