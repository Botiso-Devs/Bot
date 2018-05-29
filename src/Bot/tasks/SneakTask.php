<?php

declare(strict_types=1);

namespace Bot\tasks;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

use Bot\{
	Main, NPCHuman
};

class SneakTask extends PluginTask{

	/** @var Main $plugin */
	/** @var Entity $entity */
	private $plugin, $entity;

	public function __construct(Main $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
		parent::__construct($plugin);
	}

	public function onRun(int $tick){
		if($this->plugin->getCfg()->get("sneak") == true){
			$entity = $this->entity;

			if($entity instanceof NPCHuman){
				$entity->setSneaking(true);
			}
		}
	}
}