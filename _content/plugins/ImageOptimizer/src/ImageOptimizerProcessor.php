<?php

use PieCrust\Baker\Processors\SimpleFileProcessor;
use PieCrust\PieCrustException;
use PieCrust\Baker\IBaker;

class ImageOptimizerProcessor extends SimpleFileProcessor
{

	public function __construct()
	{
		parent::__construct('imageoptimizer', array(), array());
	}


	public function onBakeStart(IBaker $baker)
	{
		$posts = $this->pieCrust->getPostsDir();
		$bake  = $baker->getBakeDir();
		exec("find " . $posts . " -newer " . $bake . "index.html -iregex '.*\.\(png\|PNG\|gif\)' -type f -exec optipng -o7 {} ';'", $out);
		exec("find " . $posts . " -newer " . $bake . "index.html -iregex '.*\.\(jpg\|JPG\|jpeg\|JPG\)' -type f -exec jpegoptim {} ';'", $out);
	}


	protected function doProcess($inputPath, $outputPath)
	{
		$pathinfo = pathinfo($inputPath);
		if (in_array($pathinfo['extension'], $this->inputExtensions)) {
			print $inputPath;
		}

		@copy($inputPath, $outputPath);

	}


	public function isDelegatingDependencyCheck()
	{
		return false;
	}

		public function isBypassingStructuredProcessing()
	{
		return false;
	}
}