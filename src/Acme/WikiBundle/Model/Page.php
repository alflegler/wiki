<?php

namespace Acme\WikiBundle\Model;

use Acme\WikiBundle\Model\om\BasePage;

class Page extends BasePage
{
	/**
     * Get the [parentpath]."/".[pagename] value.
     *
     * @return string
     */
    public function getPath()
    {
    	if(is_null($this->parentpath)) //true only for Welcome page
        {
            return $this->pagename;
        }
        return $this->parentpath."/".$this->pagename;
    }
}
