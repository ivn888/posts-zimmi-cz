<?php

require 'cssmin.php';

use PieCrust\Baker\Processors\SimpleFileProcessor;
use PieCrust\PieCrustException;


class CssMinProcessor extends SimpleFileProcessor
{
	public function __construct()
	{
		parent::__construct('cssmin', 'css', 'css');
	}


	public function isDelegatingDependencyCheck()
	{
		return false;
	}


	protected function doProcess($inputPath, $outputPath)
	{
		$minified   = strpos($inputPath, 'min.css') !== false;

		if (!$minified) {
			$cssmin = new \CSSmin();
			$css    = file_get_contents($inputPath);
			file_put_contents($outputPath, $cssmin->run($css));
		} else {
			@copy($inputPath, $outputPath);
		}
	}


	public function isBypassingStructuredProcessing()
	{
		return false;
	}


	public function onBakeEnd()
	{
		$cssDir = $this->baker->getBakeDir() . 'css/';
		$files  = glob($cssDir . '*.css');
		$style  = 'style.min.css';

		if (file_exists($cssDir . $style)) {
			@unlink($style);
		}

		$out = fopen($cssDir . $style, 'w');


		foreach ($files as $file) {
			$filepath = pathinfo($file);

			if ($filepath['basename'] !== $style) {
				fwrite($out, file_get_contents($file));
				unlink($file);
			}
		}

		fclose($out);
	}
}