<?php

namespace petui;

use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{TextFormat, Config};
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\utils\TextFormat as TF;

class CI extends PluginBase implements Listener
{   
    
    public $formapi;
    public $blockpets;
	public $pipol;
    private $typeCache = [];
    /**
    PETS
    'bat',
    'blaze',
    'cavespider',
    'chicken',
    'cow',
    'creeper',
    'donkey',
    'enderman',
    'endermite',
    'evoker',
    'ghast',
    'horse',
    'husk',
    'irongolem',
    'llma',
    'mooshroom',
    'ocelot',
    'pig',
    'polarbear',
    'rabbit',
    'sheep',
    'skeletonhorse',
    'skeleton',
    'slime',
    'spider',
    'vex',
    'wolf',
    'zombie',
    'zombiehorse',
    'zombiepigman',
    'zombievillager',
    'elderguardian',
    'wither',
    'enderdragon'
    */
	private $vipPets = array('blaze', 'ghast', 'vex', 'wither', 'enderdragon');
	private $pubPets = array('rabbit', 'chicken', 'wolf', 'ocelot', 'pig', 'cow');
	
   	public function onEnable()
    	{

		$this->formapi = $this->getServer()->getPluginManager()->getPlugin('FormAPI');
		$this->blockpets = $this->getServer()->getPluginManager()->getPlugin('BlockPets');

		$this->ui = new petUI($this);

		$this->getLogger()->info("§l§e➡§a Cửa hàng thú cưng: Hoạt động");

		$this->saveResource('main.yml');
		$this->settings = new Config($this->getDataFolder() . "main.yml", CONFIG::YAML);

		$this->saveResource('petcount.yml');
		$this->pcount = new Config($this->getDataFolder() . "petcount.yml", CONFIG::YAML);
    	}
	
    	public function runCMD(string $c) : void
    	{
        	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $c);
    	}

    public function sendNormalMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
            if (isset($data[0])){
                $type = $this->pubPets[ $data[0] ];
		$this->storeTypeCache($player, $type);
                $this->ui->normalForm($player, $type);
		/**  
		switch ($data[0])
		{
			case 0:
				$this->storeTypeCache($player, "wolf");
                        	$this->ui->normalForm($player, "wolf");
			break;
				
			case 1:
				$this->storeTypeCache($player, "ocelot");
                        	$this->ui->normalForm($player, "ocelot");
			break;
			
			case 2:
				$this->storeTypeCache($player, "pig");
                        	$this->ui->normalForm($player, "pig");
			break;
				
			case 3:
				$this->storeTypeCache($player, "rabbit");
                        	$this->ui->normalForm($player, "rabbit");
			break;
			
			case 4:
				$this->storeTypeCache($player, "cavespider");
                        	$this->ui->normalForm($player, "cavespider");
			break
				
		}**/
               	return true;
            }
        });
          $form->setTitle('§l§c❤§6 Cửa Hàng Thú Cưng §c❤');
	foreach($this->pubPets as $pet)
	{
		$form->addButton('§l§0'.strtoupper($pet). ' : §c' . $this->getPrice($pet)); //data[0]
	}
	/**$form->addButton('§l§0Dog : §c$' . $this->getPrice("wolf")); //data[0]
	$form->addButton('§l§0Cat : §c$' . $this->getPrice("ocelot")); //data[1]
	$form->addButton('§l§0Pig : §c$' . $this->getPrice("pig")); //data[2]
	$form->addButton('§l§0Bunny : §c$' . $this->getPrice("rabbit")); //data[3]
	$form->addButton('§l§0Cave Spider : §c$' . $this->getPrice("cavespider")); //data[4]
	*/
        $form->sendToPlayer($player);
    }
	
    public function sendVIPMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
		
        	if (isset($data[0]))
	    	{
			$type = $this->vipPets[ $data[0] ];
			$this->storeTypeCache($player, $type);
			$this->ui->normalForm($player, $type);
			return true;
            	}
        });
	    
           $form->setTitle('§l§c❤§6 Cửa Hàng Thú Cưng §c❤');
	foreach($this->vipPets as $pet)
	{
		$form->addButton('§l§0'.strtoupper($pet). ' : §c' . $this->getPrice($pet));
	}
        $form->sendToPlayer($player);
    }
	
    public function sendMainMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
            if (isset($data[0])){
                switch ($data[0])
		{
			case 0:
				$this->sendNormalMenu($player);
			break;

			#case 1:
                        	#$player->sendMessage("Feature is in progress");//$this->sendExoMenu($player);
			#break;

			case 1:
				if($player->hasPermission("shs.vip.pet"))
				{
					$this->sendVIPMenu($player);
				} else {
					$player->sendMessage("§l§e[§b Thú Cưng §e]§c Bạn chưa có VIP, để mua VIP sử dụng lệnh: §d/rankshop");
				}
			break;		
		}
		return true;
            }
        });
        $form->setTitle('§l§c❤§6 Cửa Hàng Thú Cưng §c❤');
	    
        $form->addButton('§l§1•§0 Thú Cưng§1 •'); //data[0]
	$form->addButton('§l§1•§b Thú Cưng §6VIP§1 •'); //data[1]
	
        $form->sendToPlayer($player);
		
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool
    {
	  	if(!$sender instanceof Player){
		  	$sender->sendMessage("Command must be run ingame!");
		 	return true;
	  	}

	  	switch(strtolower($cmd->getName())){
		case "buypet":
                $petcount = count($this->blockpets->getPetsFrom($sender));
		$this->sendMainMenu($sender);
		$this->removeType($sender);
            break;
      }
        return true;
	}

    private function storeTypeCache(Player $player, $type): void
    {
        $this->typeCache[ $player->getName() ] = $type;
    }

    public function getType(Player $player): string
    {
        if(array_key_exists($player->getName(), $this->typeCache))
        {
            return $this->typeCache[ $player->getName() ];
        }
    }

    public function removeType(Player $player): void
    {
        if(array_key_exists($player->getName(), $this->typeCache))
        {
            unset( $this->typeCache[$player->getName()] );
        }
    }

    public function getPrice($type): int
    {
       return $this->settings->getNested("price.pets.". $type);
    }

    public function applyPetRequest(Player $player, string $petname, float $size,string $baby, string $target = null)
    {
        $eco = $this->getServer()->getPluginManager()->getPlugin('PointAPI');
        $type = $this->getType($player);
        $petprice = $this->getPrice($type);
        $pmoney = $eco->mymoney($player->getName());
        //player = Player sender , human = Player target, target = string target, plname = string sender

        if($target !== null)
        {
            $human = $this->getServer()->getPlayer($target);
            if(!$human instanceof Player) //check if the target is a Player / Online
            {
                $player->addTitle("§l§fNgười Chơi§c Offline", "§f§l Thất bại vui lòng xem lại tên: " . $target);
                    $this->removeType($player);
                        return true;
            }

            if($pmoney < $petprice) //checks if player can buy
            {
                $need = (int) $petprice - $pmoney;
                    $player->addTitle("§l§aGiao Dịch§c Thất Bại", "§f§l Bạn cần thêm§e" . $need."§a Point");
                        $this->removeType($player);
                            return true;
            }
            
            $petcount = count( $this->blockpets->getPetsFrom($human) );

            if( $petcount >= $this->settings->get('maxpets')) //checks if the player reached the max
            {
                $player->addTitle("§l§fGiao Dịch§c Thất bại", "§f§lNgười chơi này đã đủ thú cưng");
                    $this->removeType($player);
                        return true;
            }

            $target = '"'. $target . '"';
            $this->runCMD("spawnpet " .$type. " " .$petname. " " .$size. " " .$baby." ".$target);
            $human->addTitle("§l§aGiao Thú Cưng", "§l§aThú cưng §b".$type."§a đã được giao cho bạn");
            //$player->addTitle("§l§bSuccess!", "§f§lYou bought a(n) $type for§b $target");
            $eco->reducemoney($player->getName(), $petprice);
            $this->removeType($player);

            return true;

        } else {
            $petcount = count( $this->blockpets->getPetsFrom($player) );
            if( $petcount >= $this->settings->get('maxpets') && !$player->hasPermission('bypass.maxpet'))
            {
                $player->addTitle("§l§fGiao dịch§c Thất bại", "§f§lBạn đã đạt đến giới hạn thú cưng");
                    $this->removeType($player);
                        return true;
            }

            if($pmoney < $petprice)
            {
                $need = (int) $petprice - $pmoney;
                    $player->addTitle("§l§dThú cưng Kawaiii", "§f§lBạn cần §c" . $need."§f Point");
                        $this->removeType($player);
                            return true;
            }

            $plname = '"'. $player->getName() . '"';
            $this->runCMD("spawnpet " .$type. " " .$petname. " " .$size. " " .$baby." ".$plname);
            $player->addTitle("§l§bThành công!", "§f§lBạn vừa mua§e $type §ftên§b $petname");
            $eco->reducemoney($player->getName(), $petprice);
            $this->removeType($player);

            return true;
        }
    }
}
