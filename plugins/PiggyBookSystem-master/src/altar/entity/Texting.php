<?php

namespace altar\entity;

use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\entity\Human;
use pocketmine\entity\Entity;
use pocketmine\item\Item;

use pocketmine\Player;
use pocketmine\Server;

class Texting extends Human {
	
	public function attack($damage, EntityDamageEvent $source):void{
		if($source instanceof EntityDamageByEntityEvent){
			if(($damager = $source->getDamager()) instanceof Player){
				if(($item = $damager->getItemInHand())->getId() === 262){
					$this->kill();
				}
			}
		} elseif($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($damage, $source);
		}
	}
	
	public function spawnTo(Player $player):void{
		if(!isset($this->hasSpawned[$index = $player->getLoaderId()]) and $this !== $player){
			$this->hasSpawned[$index] = $player;
			
			$pk = new AddPlayerPacket();
			$pk->uuid = $this->getUniqueId();
			$pk->eid = $this->getId();
			$pk->username = "";
			
			$pk->x = $this->getX();
			$pk->y = $this->getY();
			$pk->z = $this->getZ();
			$pk->yaw = $this->getYaw();
			$pk->pitch = $this->getPitch();
			
			$pk->item = Item::get(0);
			$pk->metadata = [
			Entity::DATA_FLAGS => [0, 1 << Entity::DATA_FLAG_INVISIBLE],
			
			Entity::DATA_NO_AI => [0, 1],
			Entity::DATA_LEAD_HOLDER => [7, -1],
			Entity::DATA_LEAD => [0, 0]
			];
			$player->dataPacket($pk);
			$this->getInventory()->sendArmorContents($player);
		}
	}
	
}