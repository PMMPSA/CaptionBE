<?php

namespace altar\tile;

use pocketmine\level\format\FullChunk;
use pocketmine\tile\Spawnable;

use pocketmine\level\particle\HugeExplodeParticle;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;

use pocketmine\math\Vector3;
use pocketmine\item\Item;

use pocketmine\entity\Entity;
use pocketmine\Player;

use altar\Main;

class Altar extends Spawnable{
	
	const TAG_ID = "Altar";
	const TAG_TEMP = "temp";
	const TAG_MAX_TEMP = "maxtemp";
	
	private $texting = null;

	public function __construct(FullChunk $chunk, CompoundTag $nbt){
		parent::__construct($chunk, $nbt);
		$this->initData();
	}
	
	public function getName() : string{
		return "Altar";
	}
	
	const ALTAR_TEMP = 60;
	
	public function initData(){
 		if(!($this->namedtag instanceof CompoundTag)){
			return false;
		}
		if(!isset($this->namedtag->Temp)){
			$this->namedtag->Temp = new IntTag("Temp", 0);
		}
		if(!isset($this->namedtag->maxtemp)){
			$this->namedtag->maxtemp = new IntTag("maxtemp", self::ALTAR_TEMP);
		}
		$this->updateTemp(0);
		$this->scheduleUpdate();
	}
	
	public function getMaxTemp(){
		return $this->namedtag["maxtemp"];
	}
	
	public function setMaxTemp(int $temp = 0){
		$this->namedtag->maxtemp->setValue($temp);
	}
	
	public function getTemp(){
		return $this->namedtag["Temp"];
	}
	
	public function setTemp(int $temp = 0){
		$this->namedtag->Temp->setValue($temp);
	}
	
	const MIN_DISTANCE = 6;
	
	public function checkArea() : bool{
		$level = $this->getLevel();
		$count = 0;
		foreach($level->getPlayers() as $player){
			if($player->distance($this->getBlock()) <= self::MIN_DISTANCE){
				$count++;
			}
		}
		if($count >= 1){
			return true;
		}
		return false;
	}
	
	private $temped = 0;
	
	public function onUpdate(){
		if($this->closed === true){
			return false;
		}
		if($this->temped < 20){
			$this->temped++;
			return true;
		}
		$this->temped = 0;
		$this->timings->startTiming();
		if(!($this->chunk instanceof FullChunk)){
			return false;
		}
		if($this->checkArea()){
			$max = $this->getMaxTemp();
			$temp = $this->getTemp();
			if($temp >= $max){
				$this->setTemp();
				if(($level = $this->getLevel()) === null){
					return false;
				}
				$level->addParticle(new HugeExplodeParticle($this));
				$level->dropItem($this->add(0, 2), Item::get(288, 0, 1)->setCustomName("§f§6Caixa Misteriosa: §bAproveite"));
				$level->dropItem($this->add(0, 2), Item::get(146, 0, 1)->setCustomName("§f§6Caixa Misteriosa: §bAproveite"));
				$this->temped = -100;
				return true;
			} else {
				$this->setTemp($temp + 1);
			}
		}
		$this->updateTemp($this->getTemp());
		$this->timings->stopTiming();
		return true;
	}
	
	private function updateTemp(int $temp = 0){
		$this->getTexting()->setDataProperty(2, Entity::DATA_TYPE_STRING, str_replace(["{temp}", "{max-temp}", "{barra}", "{tempo}"], [$temp, $this->getMaxTemp(), $this->getBarraStatus(), $this->getTempFormat($this->getMaxTemp() - $temp)], Main::$texting));
	}
	
	const COUNT_BLOCKS = 8;
	
	public function getBarraStatus(){
		$max = $this->getMaxTemp();
		$temp = $this->getTemp();
		
		$unid = $max / self::COUNT_BLOCKS;
		$status = "";
		
		if($temp < $max){
			for($i = 1; $i < self::COUNT_BLOCKS; $i++){
				$cust = ($temp - ($i * $unid));
				if($cust >= $unid){
					$status .= "§8█";
				} elseif($cust >= ($unid / 2)){
					$status .= "§8▒";
				} else {
					$status .= "§7█";
				}
			}
		} else {
			$status = str_repeat("§a█", self::COUNT_BLOCKS - 1);
		}
		$format = $status . " " . ceil(($temp * 100) / $max) . "%";
		return $format;
	}
	
	public function getTempFormat(int $time){
		$seg = (int)($time % 60);
		$time /= 60;
		$min = (int)($time % 60);
		$time /= 60;
		$hora = (int)($time % 24);
		if($seg < 10){
			$seg = "0" . $seg;
		}
		if($min < 10){
			$min = "0" . $min;
		}
		if($hora < 10){
			$hora = "0" . $hora;
		}
		return "$hora:$min:$seg";
	}
	
	public function getTexting(){
		if($this->texting !== null){
			return $this->texting;
		}
		if(($level = $this->getLevel()) !== null){
			foreach($level->getEntities() as $entity){
				if(isset($this->namedtag->dates) and $this->namedtag["dates"] == $this->getDatesFrom()){
					$this->texting = $entity;
					return $this->texting;
				}
			}
		}
		$nbt = new CompoundTag();
		
		$nbt->Pos = new ListTag("", [
		new DoubleTag("", (int) $this->getX() + 0.5),
		new DoubleTag("", (int) $this->getY()),
		new DoubleTag("", (int) $this->getZ() + 0.5)
		]);
		$nbt->Rotation = new ListTag("", [
		new DoubleTag("", 0),
		new DoubleTag("", 0)
		]);
		$nbt->Motion = new ListTag("", [
		new DoubleTag("", 0),
		new DoubleTag("", 0),
		new DoubleTag("", 0)
		]);
		$nbt->dates = new StringTag("dates", $this->getDatesFrom());
			
		$texting = Entity::createEntity("Texting", $this->chunk, $nbt);
		$texting->spawnToAll();
		$texting->setNameTag("");
			
		$this->texting = $texting;
		return $this->texting;
	}
	
	public function getDatesFrom() : string{
		$dates = "[" . self::TAG_ID . "]";
		if(($pos = $this) instanceof Vector3){
			foreach(["x", "y", "z"] as $k => $v){
				$dates .= "(" . strtoupper($v) . ":" . ((int) $this->{$v}) . ")";
			}
		}
		return $dates;
	}
	
	public function close(){
		parent::close();
		$this->getTexting()->close();
	}

	public function getSpawnCompound(){
		$nbt = new CompoundTag();
		$nbt->id = new StringTag("id", self::TAG_ID);
		
		if(($pos = $this) instanceof Vector3){
			$nbt->x = new IntTag("x", (int) $pos->x);
			$nbt->y = new IntTag("y", (int) $pos->y);
			$nbt->z = new IntTag("z", (int) $pos->z);
		}
		
		return $nbt;
	}
	
}