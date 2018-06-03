<?php

declare(strict_types=1);

namespace Bot;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
	Config, TextFormat
};

use Bot\tasks\NPCTask;

class Main extends PluginBase{

	public function onEnable(): void{
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		Entity::registerEntity(NPCHuman::class, true);
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		if(!$sender->isOp()){
			return false;
		}

		if(count($args) < 1){
			#$sender->sendMessage("- /bot spawn <name>");
			#$sender->sendMessage("- /bot remove");
			$sender->sendMessage("Usage: /bot <name>");
			return false;
		}

		$this->spawnNPC($sender, $args[0]);
		$sender->sendMessage(TextFormat::GREEN . "Spawned Bot: " . $args[0]);

		#switch($args[0]){
			#case "spawn":
			#case "create":
			#if(count($args) < 2){
				#$sender->sendMessage("Usage: /bot spawn <name>");
				#return false;
			#}

			#$this->spawnNPC($sender, $args[1]);
			#$sender->sendMessage(TextFormat::GREEN . "Spawned Bot: " . $args[1]);
			#break;
			#case "remove":
			#case "delete":
			#case "del":
			#case "kill":
			#if(!in_array($sender->getName(), $this->remove)){
				#$this->remove[] = $sender->getName();
				#$sender->sendMessage(TextFormat::AQUA . "Turned Remove Mode " . TextFormat::GREEN . "[ON]");
			#}elseif(in_array($sender->getName(), $this->remove)){
				#unset($this->remove[array_search($sender->getName(), $this->remove)]);
				#$sender->sendMessage(TextFormat::AQUA . "Turned Remove Mode " . TextFormat::RED . "[OFF]");
			#}
			#break;
			#default:
			#$sender->sendMessage("- /bot spawn <name>");
			#$sender->sendMessage("- /bot remove");
			#break;
		#}
		return true;
	}

	public function getCfg(): Config{
		return new Config($this->getDataFolder() . "config.yml", Config::YAML);
	}

	public function spawnNPC(Player $player, string $name): void{
		$nbt = Entity::createBaseNBT($player, null, 2, 2);
		$nbt->setTag($player->namedtag->getTag("Skin"));
		$npc = new NPCHuman($player->getLevel(), $nbt);
		$npc->setNameTag($name);
		$npc->setNameTagAlwaysVisible(true);
		$npc->spawnToAll();
	}
}