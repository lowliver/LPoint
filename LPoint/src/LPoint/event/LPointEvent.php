<?php

namespace LPoint\event;

use LPoint\LPoint;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;


class LPointEvent extends PluginEvent implements Cancellable{
	
	public function __construct(LPoint $plugin){}
	
}
