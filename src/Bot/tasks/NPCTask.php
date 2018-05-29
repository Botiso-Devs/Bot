<?php

declare(strict_types=1);

namespace Bot\tasks;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

use Bot\{
	Main, NPCHuman
};

class NPCTask extends PluginTask{

	/** @var Main $plugin */
	/** @var Entity $entity */
	private $plugin, $entity;

	public function __construct(Main $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
		parent::__construct($plugin);
	}

	public function onRun(int $tick): void{
		$entity = $this->entity;
		
		if($entity instanceof NPCHuman){
			$this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new SneakTask($this->plugin, $entity), 50);
			$this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new UnSneakTask($this->plugin, $entity), 50);
			$this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new ParticleTask($this->plugin, $entity), 20);
		}
	}
}