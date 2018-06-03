<?php

declare(strict_types=1);

namespace Bot\tasks;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

use Bot\{
	Main, NPCHuman
};

class UnSneakTask extends PluginTask{

	/** @var Main $plugin */
	/** @var Entity $entity */
	private $plugin, $entity;

	public function __construct(Main $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
		parent::__construct($plugin);
	}

	public function onRun(int $tick): void{
		if($this->plugin->getCfg()->get("unsneak") == true){
			$entity = $this->entity;

			if($entity instanceof NPCHuman){
				$entity->setSneaking(false);
			}
		}
	}
}