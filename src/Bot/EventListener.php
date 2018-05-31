<?php

declare(strict_types=1);

namespace Bot;

use pocketmine\Player;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\{
	AnimatePacket, MovePlayerPacket
};
use pocketmine\event\entity\{
	EntitySpawnEvent, EntityDamageEvent, EntityDamageByEntityEvent
};
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;

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
		if($this->plugin->getCfg()->get("damage") == false){
			if($e instanceof EntityDamageByEntityEvent){
				$e->setCancelled();
			}
		}
	}

	public function onMove(PlayerMoveEvent $e): void{
		if($this->plugin->getCfg()->get("rotation") == true){
			$player = $e->getPlayer();
			$level = $player->getLevel();
			$boundingbox = $player->getBoundingBox();
			$from = $e->getFrom();
			$to = $e->getTo();
			$distance = $this->plugin->getCfg()->get("distance");

			if($from->distance($to) < 0.1) return;
			foreach($level->getNearByEntities($boundingbox->grow($distance, $distance, $distance), $player) as $entity){
				if($entity instanceof Player) continue;

				$xdiff = $player->x - $entity->x;
				$ydiff = $player->y - $entity->y;
				$zdiff = $player->z - $entity->z;
				$angle = atan2($zdiff, $xdiff);
				$yaw = (($angle * 180) / M_PI) - 90;
				$v = new Vector2($entity->x, $entity->z);
				$dist = $v->distance($player->x, $player->z);
				$angle = atan2($dist, $ydiff);
				$pitch = (($angle * 180) / M_PI) - 90;

				if($entity instanceof NPCHuman){
					$pk = new MovePlayerPacket();
					$pk->entityRuntimeId = $entity->getId();
					$pk->position = $entity->asVector3()->add(0, $entity->getEyeHeight(), 0);
					$pk->yaw = $yaw;
					$pk->pitch = $pitch;
					$pk->headYaw = $yaw;
					$pk->onGround = $entity->onGround;
					$player->dataPacket($pk);
				}
			}
		}
	}
}