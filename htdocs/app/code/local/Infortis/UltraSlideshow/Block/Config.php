<?php
class Infortis_UltraSlideshow_Block_Config extends Mage_Core_Block_Template
{
	public function getSlideshowCfg()
	{
		$h = Mage::helper('ultraslideshow');
		
		$cfg = array();
		$cfg['fx']			= "'" . $h->getCfg('general/fx') . "'";
		$cfg['easing']		= "'" . $h->getCfg('general/easing') . "'";
		$cfg['timeout']		= intval($h->getCfg('general/timeout'));
		$cfg['speed']		= intval($h->getCfg('general/speed'));
		$cfg['pause']		= $h->getCfg('general/pause');
		$cfg['loop']		= $h->getCfg('general/loop');
		
		return $cfg;
	}
}