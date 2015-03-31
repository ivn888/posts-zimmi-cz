<?php

require 'src/ImageOptimizerProcessor.php';

use PieCrust\PieCrustPlugin;

class ImageOptimizerPlugin extends PieCrustPlugin
{
    public function getName()
    {
        return 'ImageOptimizerPlugin';
    }


    public function getProcessors()
    {
    	return array(new ImageOptimizerProcessor());
    }
}