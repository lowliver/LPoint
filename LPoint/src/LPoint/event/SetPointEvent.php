<?php

namespace LPoint\event;

class SetPointEvent extends LPointEvent{
	
	protected $player;
	protected $point;
	
	public function __construct(string $player, float $point){
		$this->player = $player;
		$this->point = $point;
	}
	
	public function getPlayer() : string{
		return $this->player;
	}
	
	public function getAmount() : float{
		return $this->point;
	}

}
