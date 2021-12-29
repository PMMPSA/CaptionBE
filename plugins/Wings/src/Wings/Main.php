<?php

namespace Wings;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use Wings\Tasks\{GreenWing, RedWing, FlameWing, Angel};
use pocketmine\Player;

class Main extends PluginBase{

	public $tasks = [];
	public $particle1 = array("GreenWing");
	public $particle2 = array("RedWing");
	public $particle3 = array("FlameWing");

	public function onEnable () : void{
		$this->getServer()->getLogger()->info("§l§aHero§bMC§e>§r§a Wings Enable");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		if ($cmd == "wing"){
			if(empty($args[0]) || empty($args[1])){
				$sender->sendMessage("§9•§4 Wing Red. §e/wing red <on|off>");
				$sender->sendMessage("§9•§a Wing Green §e/wing green <on|off>");
				$sender->sendMessage("§9•§6 Wing Flame. §e/wing Flame <on|off>");
				$sender->sendMessage("§9•§b Angel Particle §e/wing angel <on|off>");
			return true;
		}
			if(!empty($args[0])){
				switch($args[0]){
					case "angel":
						if(!$sender->hasPermission("angel.perm")){
							$sender->sendMessage(">§r§c Bạn không có quyền dùng Wing §aGreen");
						return true;
					}
						if($args[1] == "on"){
						$task = new Angel($sender);
						$this->tasks[$sender->getId()] = $task;
						$this->getScheduler()->scheduleRepeatingTask($task, 10);
							$sender->sendMessage(">§r§a Đã bật Wing Green");
						}
							if($args[1] == "off"){
								$task = $this->tasks[$sender->getId()];
								unset($this->tasks[$sender->getId()]);
								$task->getHandler()->cancel();
								$sender->sendMessage(">§r§c Đã tắt §dAngel Partie");
							}
					break;
					case "green":
						if(!$sender->hasPermission("green.wing")){
							$sender->sendMessage(">§r§c Bạn không có quyền dùng Wing §aGreen");
						return true;
					}
						if($args[1] == "on"){
						$task = new GreenWing($sender);
						$this->tasks[$sender->getId()] = $task;
						$this->getScheduler()->scheduleRepeatingTask($task, 10);
							$sender->sendMessage(">§r§a Đã bật Wing Green");
						}
							if($args[1] == "off"){
								$task = $this->tasks[$sender->getId()];
								unset($this->tasks[$sender->getId()]);
								$task->getHandler()->cancel();
								$sender->sendMessage(">§r§c Đã tắt Wing §aGreen");
							}
					break;
					case "red":
						if(!$sender->hasPermission("red.wing")){
							$sender->sendMessage(">§r§c Bạn không có quyền dùng Wing §4Red");
						return true;
					}
						if($args[1] == "on"){
						$task = new RedWing($sender);
						$this->tasks[$sender->getId()] = $task;
						$this->getScheduler()->scheduleRepeatingTask($task, 10);
							$sender->sendMessage(">§r§a Đã bật Wing §4Red");
						}
							if($args[1] == "off"){
								$task = $this->tasks[$sender->getId()];
								unset($this->tasks[$sender->getId()]);
								$task->getHandler()->cancel();
								$sender->sendMessage(">§r§c Đã tắt Wing §4Red");
							}
					break;
					case "flame":
						if(!$sender->hasPermission("flame.wing")){
							$sender->sendMessage(">§r§c Bạn không có quyền dùng Wing §6Flame");
						return true;
					}
						if($args[1] == "on"){
						$task = new FlameWing($sender);
						$this->tasks[$sender->getId()] = $task;
						$this->getScheduler()->scheduleRepeatingTask($task, 10);
							$sender->sendMessage(">§r§a Đã bật Wing §6Flame");
						}
							if($args[1] == "off"){
								$task = $this->tasks[$sender->getId()];
								unset($this->tasks[$sender->getId()]);
								$task->getHandler()->cancel();
								$sender->sendMessage(">§r§c Đã tắt Wing §6Flame");
							}
					break;
					default:
				$sender->sendMessage("§9•§4 Wing Red. §e/wing red");
				$sender->sendMessage("§9•§a Wing Green §e/wing green");
				$sender->sendMessage("§9•§6 Wing Flame. §e/wing Flame");
								$sender->sendMessage("§9•§b Angel Particle §e/wing angel <on|off>");
						break;
				}
			}
		}
		return true;
	}
	
	public function onQuit(PlayerQuitEvent $e) {
		$player = $e->getPlayer();
		$task = $this->tasks[$player->getId()];			
		unset($this->tasks[$player->getId()]);
		$task->getHandler()->cancel();
	}
}
