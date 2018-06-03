<?php

declare(strict_types=1);

namespace Bot;

use pocketmine\Player;
use pocketmine\math\Vector2;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\{
	AnimatePacket, MovePlayerPacket, MoveEntityPacket
};
use pocketmine\event\entity\{
	EntitySpawnEvent, EntityDamageEvent, EntityDamageByEntityEvent
};
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

use Bot\tasks\NPCTask;

class EventListener implements Listener{

	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onEntitySpawn(EntitySpawnEvent $e): void{
		$entity = $e->getEntity();

		if($entity instanceof NPCHuman){
			$this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new NPCTask($this->plugin, $entity), 200);
		}
	}

	public function onSwing(EntityDamageEvent $e): void{
		if($this->plugin->getCfg()->get("swing") == true){
			$entity = $e->getEntity();

			if($e instanceof EntityDamageByEntityEvent){
				$damager = $e->getDamager();

				if($entity instanceof NPCHuman){
					$pk = new AnimatePacket();
					$pk->entityRuntimeId = $entity->getId();
					$pk->action = AnimatePacket::ACTION_SWING_ARM;
					$damager->dataPacket($pk);
				}
			}
		}
	}

	public function onDamage(EntityDamageEvent $e): void{
		$entity = $e->getEntity();

		if($this->plugin->getCfg()->get("damage") == false){
			if($e instanceof EntityDamageByEntityEvent){
				$e->setCancelled();
			}
		}
	}

    public function onPlayerMove(PlayerMoveEvent $e): void{
    	if($this->plugin->getCfg("rotation") == true){
    		$player = $e->getPlayer();
    		$from = $e->getFrom();
    		$to = $e->getTo();
    		$distance = $this->plugin->getCfg()->get("rotate-distance");

    		if($from->distance($to) < 0.1) return;
    		foreach($player->getLevel()->getNearbyEntities($player->getBoundingBox()->grow($distance, $distance, $distance), $player) as $entity){
    			if($entity instanceof NPCHuman){
    				$pk = new MoveEntityPacket();
    				$v = new Vector2($entity->x, $entity->z);
    				$yaw = ((atan2($player->z - $entity->z, $player->x - $entity->x) * 180) / M_PI) - 90;
    				$pitch = ((atan2($v->distance($player->x, $player->z), $player->y - $entity->y) * 180) / M_PI) - 90;
    				$pk->entityRuntimeId = $entity->getId();
    				$pk->position = $entity->asVector3()->add(0, 1.5, 0);
    				$pk->yaw = $yaw;
    				$pk->headYaw = ((atan2($player->z - $entity->z, $player->x - $entity->x) * 180) / M_PI) - 90;
    				$pk->pitch = $pitch;
    				$player->dataPacket($pk);
    				$entity->setRotation($yaw, $pitch);
    			}
    		}
    	}
    }
}