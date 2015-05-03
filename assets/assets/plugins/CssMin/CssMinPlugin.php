<?php

require 'src/CssMinProcessor.php';

use PieCrust\PieCrustPlugin;

class CssMinPlugin extends PieCrustPlugin
{
    public function getName()
    {
        return 'CssMinPlugin';
    }


    public function getProcessors()
    {
    	return array(new CssMinProcessor());
    }
}