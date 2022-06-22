<?php

$readdir = null;
$readdir = function ($path) use (&$readdir) {
	$dirs = [];
	foreach (scandir($path) as $dir) {
		if ($dir === '.' || $dir === '..')
			continue;
		$filename = $path . DIRECTORY_SEPARATOR . $dir;
		if (is_dir($filename))
			$dirs[] = $filename;
		else if (false !== strpos($dir, '.js'))
			echo file_get_contents($filename);
	}

	foreach ($dirs as $filename) {
		$readdir($filename);
	}
};

header('content-type: application/javascript');

$jsPath = realpath(__DIR__ . '/../');

$readdir($jsPath . '/core');
$readdir($jsPath . '/app');
$readdir($jsPath . '/vendor');
