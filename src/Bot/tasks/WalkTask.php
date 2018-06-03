<?php

declare(strict_types=1);

namespace Bot\tasks;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector3;

use Bot\{
	Main, NPCHuman
};

class WalkTask extends PluginTask{

	/** @var Main $plugin */
	/** @var Entity $entity */
	private $plugin, $entity;

	public function __construct(Main $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
		parent::__construct($plugin);
	}

	public function onRun(int $tick){
		if($this->plugin->getCfg()->get("walk") == true){
			$entity = $this->entity;
			$distance = $this->plugin->getCfg()->get("walk-distance");

			if($entity instanceof NPCHuman){
				switch($entity->getDirection()){
					case 0:
					$entity->setMotion(new Vector3($distance, 0, 0));
					break;
					case 1:
					$entity->setMotion(new Vector3(0, 0, $distance));
					break;
					case 2:
					$entity->setMotion(new Vector3(-$distance, 0, 0));
					break;
					case 3:
					$entity->setMotion(new Vector3(0, 0, -$distance));
					break;
				}
			}
		}
	}
}