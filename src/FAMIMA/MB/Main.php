<?php

namespace FAMIMA\MB;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerInteractEvent;
class Main extends PluginBase implements Listener
{

	private $server;

	public function onEnable()
	{
		$this->server = $this->getServer();
		$this->server->getPluginManager()->registerEvents($this, $this);
		$this->MBTask();
	}

	public function onJoin(PlayerLoginEvent $event)
	{
		$n = $event->getPlayer()->getName();
		$this->Boost[$n] = 0;
		$this->sp[$n] = 1;
	}

	public function onTap(PlayerInteractEvent $event)
	{
		$p = $event->getPlayer();
		$n = $p->getName();
		if($this->Boost[$n] >= 150){
			$this->Boost[$n] = 0;
			$this->sp[$n] = 2;
			$this->server->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"ZeroTask"], [$n]), 20*10);
		}
	}

	public function MBTask()
	{
		foreach($this->server->getOnlinePlayers() as $p){
			$x = $p->x;
			$y = $p->y;
			$z = $p->z;
			$v = new Vector3($x, $y-1, $z);
			$l = $p->getLevel();
			if($l->getBlock($v)->getID() == 159 and $p->getItemInHand()->getID() == 280){
				$d = $p->getYaw();
				$n = $p->getName();
				$x = cos(deg2rad($d));
				$z = sin(deg2rad($d));
				$ya = 90 - abs($p->getPitch());
				$ya = $ya/45;
				$p->setMotion(new Vector3(-$z*$ya*$this->sp[$n], -0.3, $x*$ya*$this->sp[$n]));
				if($this->Boost[$n] < 150 and $this->sp[$n] == 1){
					$this->Boost[$n]++;
					$p->sendPopup("Tapで加速!");
				}
				$p->sendTip($this->Boost[$n]. " / 150");
			}
		}
		$this->server->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"MBTask"], []), 2);
	}
	public function ZeroTask($n)
	{
		$this->sp[$n] = 1;
	}
}