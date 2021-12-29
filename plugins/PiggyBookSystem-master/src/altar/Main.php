<?php

namespace altar;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;

use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\entity\Entity;
use pocketmine\tile\Tile;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\Player;

use altar\entity\Texting;
use altar\tile\Altar;

class Main extends PluginBase implements Listener{
	
	public static $texting = "§l§aALTAR DO PODER§r\n§7{barra}\n{tempo}";
	public static $tempMax = 3600;
	
	public static $base = [
	 "tempo" => [
	  "horas" => 1,
	  "minutos" => 0,
	  "segundos" => 0,
	 ],
	 "message" => "§l§c ALTAR DAS CAIXAS §r\n§7    {barra}\n        §f{tempo}"
	];
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		if(($folder = $this->getDataFolder()) !== ""){
			@mkdir($folder);
			$data = new Config($folder . "config.yml", Config::YAML, self::$base);
			
			$temp = $data->get("tempo");
			self::$tempMax = ceil(($temp["horas"] * 60) * 60);
			self::$tempMax += ceil($temp["minutos"] * 60);
			self::$tempMax += ceil($temp["segundos"]);
			
			self::$texting = $data->get("message");
		}
		Entity::registerEntity(Texting::class, true);
		Tile::registerTile(Altar::class);
	}
	
	public function place(BlockPlaceEvent $e){
		if($e->isCancelled()){
			return false;
		}
		$block = $e->getBlock();
		if($block->getId() === 120){
			if(($level = $block->getLevel()) === null){
				return false;
			}
			$e->setCancelled();
			$level->setBlock($block, $block, true, true);
			
			$nbt = new CompoundTag();
			$nbt->id = new StringTag("id", Altar::TAG_ID);
			if($block instanceof Vector3){
				$nbt->x = new IntTag("x", (int) $block->getX());
				$nbt->y = new IntTag("y", (int) $block->getY());
				$nbt->z = new IntTag("z", (int) $block->getZ());
			}
			if(($max = self::$tempMax) >= 0){
				$nbt->maxtemp = new IntTag("maxtemp", $max);
			}
			$altar = Tile::createTile("Altar", $level->getChunk($block->getX() >> 4, $block->getZ() >> 4), $nbt);
			$altar->spawnToAll();
		}
	}
	
	public static function center(string $input){
        $clear = TextFormat::clean($input);
        $lines = explode("\n", $clear);
        $max = max(array_map("strlen", $lines));
        $lines = explode("\n", $input);
        foreach($lines as $key => $line){
            $lines[$key] = str_pad($line, $max + self::renkSayisi($line), " ", STR_PAD_LEFT);
        }
        return implode("\n", $lines);
    }

    public static function renkSayisi($yazi){
        $renkler = "abcdef0123456789lo";
        $sayi = 0;
        for($i=0; $i<strlen($renkler); $i++){
            $sayi += substr_count($yazi, "§".$renkler{$i});
        }
        return $sayi;
    }
	
}