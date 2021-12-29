<?php

namespace petui;

use petui\CI;
use pocketmine\Player;

class petUI
{
    public $main;
	
	public function __construct(CI $pg) {
        	$this->main = $pg;
    	}

    	public function customForm(Player $player, $wooosh)
    	{
		$form = $this->main->formapi->createCustomForm(function (Player $player, array $data) {
		    if( isset($data[1]) )
		    {
			$pref = $data[1]; //pet name
			$nn = $data[2]; //baby
			$poor = $data[3]; //target name

			if(strlen($pref) <= 0 or strlen($pref) >= 9 ) {
				$player->sendMessage('§l§e[§b Thú Cưng §e] Lỗi! Cho phép 1 - 8 ký tự');
				return true;
			}

			$baby = 'false';
			if($nn) {
				$baby = 'true';
			}

			$target = null;
			if($poor !== "") {
				$target = $poor;
			}

				$this->main->applyPetRequest($player, $pref, (float) 1.0, $baby, $target);
		    }
			return true;
		});

		$form->setTitle('§l§c❤ §eThú Cưng§c ❤');
		//data[0]
		$skadoossh = $this->main->getPrice($wooosh);
		$form->addLabel("§fLoại vật nuôi:§a $wooosh §f[§a $skadoossh §f]§r\n§cGhi chú:§b Một số thú cưng bạn cần phải tái sinh để trở thành Baby, còn một số không có dạng Baby");
		//$data[1]
		$form->addInput("§o§lTên Thú Cưng: 8 Kí tự");
		//$data[2]
		$form->addToggle("§o§fDạng Baby?", false);
		//$data[3]
		$form->addInput("§oQuà tặng cho người chơi (không bắt buộc | Tự động)");
		$form->sendToPlayer($player);
    	}
	
	public function normalForm(Player $player, $wooosh)
    	{
		$form = $this->main->formapi->createCustomForm(function (Player $player, array $data) {
		    if( isset($data[1]) ) {

			$pref = $data[1]; //pet name
			$poor = $data[2]; //target name

			if(strlen($pref) <= 0 or strlen($pref) >= 9 ) {
			    $player->sendMessage('§l§e[§b Thú Cưng §e] Lỗi! Cho phép 1 - 8 ký tự');
			    return true;
			}

			$target = null;
			if($poor !== "") {
			    $target = $poor;
			}

			$this->main->applyPetRequest($player, $pref, (float) 1.0, "false", $target);
		    }
			return true;	
		});
        
		$form->setTitle('§l§c❤ §eThú Cưng§c ❤');		//data[0]
		$skadoossh = $this->main->getPrice($wooosh);
		$form->addLabel("§fLoại Thú cưng:§a $wooosh §f[§a $skadoossh §f]");
		//$data[1]
		$form->addInput("§oPetName §8(max of 8 chars.)§c*");
		//$data[2]
		$form->addInput("§oQuà tặng cho người chơi (Không bắt buộc | Tự động)");
		$form->sendToPlayer($player);
    	}
}
