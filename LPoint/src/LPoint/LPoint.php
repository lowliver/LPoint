<?php

namespace LPoint;

use LPoint\event\AddPointEvent;
use LPoint\event\SubtractPointEvent;
use LPoint\event\SetPointEvent;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\Player;

//테스트

class LPoint extends PluginBase implements Listener{
	
	public static $instance;
	
	protected $rank_to_player;
	
	public static function getInstance() : LPoint{
		return self::$instance;
	}
	
	public function onLoad(){
		self::$instance = $this;
	}
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		@mkdir($this->getDataFolder());
		$this->PlayerPoint = (new Config($this->getDataFolder() . "PlayerPoint.yml", Config::YAML));
		$this->pp = $this->PlayerPoint->getAll();
		
		$this->resetRank();
	}
	
	public function isJoined($player) : bool{
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		if(isset($this->pp [$name])){
			return true;
		}
		return false;
	}
	
	public function getPoint($player) : int{
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		if($this->isJoined($name)){
			return (int)$this->pp [$name];
		}
		return 0;
	}
	
	public function getRank($player) : ?int{
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		if($this->isJoined($name)){
			$config = $this->pp;
			arsort($config);
			$key = array_keys($config);
			$target = array_search($name, $key)+1;
			return $target;
		}
		return false;
	}
	
	public function getRanker(int $rank) : ?string{
		$config = $this->pp;
		if(count($config) >= $rank){
			return $this->rank_to_player[$rank];
		}
		return false;
	}
	
	public function setPoint($player, int $point){
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		$ev = new setPointEvent($name, $point);
		$ev->call();
		$this->pp [$name] = $point;
		$this->saveAll();
		$this->resetRank();
	}
	
	public function addPoint($player, int $point){
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		$ev = new AddPointEvent($name, $point);
		$ev->call();
		if($this->isJoined($name)){
			$this->setPoint($player, ((int)$this->pp [$name])+$point);
		}else{
			$this->setPoint($player, $point);
		}
	}
	
	public function subtractPoint($player, int $point){
		$name = strtolower($player instanceof Player ? $player->getName() : $player);
		$ev = new SubtractPointEvent($name, $point);
		$ev->call();
		if($this->isJoined($name) and (int)$this->pp [$name] >= $point){
			$this->setPoint($player, ((int)$this->pp [$name])-$point);
		}else{
			$this->setPoint($player, 0);
		}
	}
	
	public function countPlayer() : int{
		$config = $this->pp;
		$count = count($config);
		return $count;
	}
	
	protected function resetRank(){
		$config = $this->pp;
		arsort($config);
		$rank = 0;
		foreach($config as $name => $money){
			$rank++;
			$this->rank_to_player[$rank] = "{$name}";
		}
	}
	
	protected function saveAll(){
		$this->PlayerPoint->setAll($this->pp);
	    $this->PlayerPoint->save();
	}
	
}