<?php

namespace radondev\AntiEnchantmentAbuse\listener;

use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;
use radondev\AntiEnchantmentAbuse\AntiEnchantmentAbuse;

/**
 * Author: radondev
 * Project: AntiEnchantmentAbuse
 * Date: 27.11.18
 */


class InventoryChangeListener implements Listener
{
    //private $prefix;
  /*  private $maxEnchantmentLevel;
    private $setEnchantmentLevel;*/

    public function __construct(AntiEnchantmentAbuse $antiEnchantmentAbuse)
    {
        $this->prefix = TextFormat::GRAY . "[" . TextFormat::GREEN . "AntiEnchantmentAbuse" . TextFormat::GRAY . "] " . TextFormat::RESET;
        $this->maxEnchantmentLevel = 3;
        $this->setEnchantmentLevel = 2;
    }

    public function onChange(EntityInventoryChangeEvent $event): void
    {
        if ($event->getEntity() instanceof Player) {
            $player = $event->getEntity();

            if (!$player->hasPermission("antienchantmentabuse")) {
                foreach ($event->getNewItem()->getEnchantments() as $enchantment) {
                    $level = $event->getNewItem()->getEnchantmentLevel($enchantment);
                      $id = $event->getNewItem()->getEnchantment($enchantment)->getId();
				/*	  if($id == 10 || $id == 12 || $id == 18){
					  if ($level > $this->maxEnchantmentLevel) {
                        if ($this->setEnchantmentLevel) {
                            $event->getNewItem()->addEnchantment($enchantment, $level);

                           // $player->sendMessage($this->prefix . "Xóa vật phẩm: " . $event->getNewItem()->getName());
                           // $player->sendMessage($this->prefix . "Vật phẩm có enchant không lệ level: " . $this->maxEnchantmentLvel);
                        } else {
                            $newItem = new Item(Item::AIR);

                            $event->setNewItem($newItem);

                            $player->sendMessage($this->prefix . "Xóa Vật Phẩm: " . $newItem->getName());
                            $player->sendMessage($this->prefix . "Vật phẩm có enchant không lệ level: §6" . $this->maxEnchantmentLvel);
                           }
                    }
                }*/
					  if($id == 10 || $id == 12 || $id == 18){
						  if($level > 3){
							    $newItem = new Item(Item::AIR);

                            $event->setNewItem($newItem);

                            $player->sendMessage($this->prefix . "Xóa Vật Phẩm: " . $newItem->getName());
                            $player->sendMessage($this->prefix . "Vật phẩm có enchant không lệ level: §6" . $this->maxEnchantmentLvel);
          
						  }
					  }
				}
            }
        }
    }
}