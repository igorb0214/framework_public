<?php

use core\Application;


/**
 * @param string $dir
 * @return array
 */
function getDirContents(string $dir): array {

	$handle = opendir($dir);
	if (!$handle) return [];

	$contents = [];
	while ($entry = readdir($handle)) {

		if($entry=='.' || $entry=='..') continue;

		$entry = $dir.DIRECTORY_SEPARATOR.$entry;
		if (is_file($entry)) {
			$contents[] = $entry;
		}
		else if (is_dir($entry)) {
			$contents = array_merge($contents, getDirContents($entry));
		}
	}

	closedir($handle);
	return $contents;
}

/**
 * @param string $str
 * @return string
 */
function replaceSlashes(string $str): string {
	return $file = str_replace("\\", "/", $str);
}

/**
 * @param string $className
 */
function autoload(string $className): void {

	$file = replaceSlashes(__DIR__ . '/' . $className . '.php');

	if(file_exists($file)) {
		require_once($file);
	}

	return;
}

/**
 * @param string $className
 */
function autoloadMvc(string $className): void {

	$file = replaceSlashes(Application::getInstance()->getAppRootPath() . '/' . $className . '.php');

	if(file_exists($file)) {
		require_once($file);
	}
}

/**
 * @param string $className
 */
function autoloadLibrary(string $className): void {

	foreach (getDirContents(__DIR__ . '/library') as $path) {
		if ($className == basename($path, ".php")) {
			require_once(replaceSlashes($path));
			return;
		}
	}
}

/**
 * @param string $className
 */
function autoloadLibraryWithNameSpace(string $className): void {

	$file = __DIR__ . '/library/' . $className . '.php';

	if(file_exists($file)) {
		require_once($file);
	}
}


spl_autoload_register('autoload');
spl_autoload_register('autoloadMvc');
spl_autoload_register('autoloadLibrary');
spl_autoload_register('autoloadLibraryWithNameSpace');