<?php

namespace radondev\AntiEnchantmentAbuse;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use radondev\AntiEnchantmentAbuse\listener\InventoryChangeListener;

/**
 * Author: radondev
 * Project: AntiEnchantmentAbuse
 * Date: 25.11.18
 */
class AntiEnchantmentAbuse extends PluginBase
{
 //   private $prefix;

    public function onEnable()
    {
      //  $this->prefix = TextFormat::GRAY . "[" . TextFormat::GREEN . "AntiEnchantmentAbuse" . TextFormat::GRAY . "] " . TextFormat::RESET;
        $this->getServer()->getPluginManager()->registerEvents(new InventoryChangeListener(), $this);
    }

  /*  public function getPrefix(): string
    {
        return $this->prefix;
    }*/
}