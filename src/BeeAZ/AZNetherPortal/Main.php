<?php

declare(strict_types=1);

namespace BeeAZ\AZNetherPortal;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\hell\Nether;
use pocketmine\world\WorldCreationOptions;
use pocketmine\scheduler\ClosureTask;
use BeeAZ\AZNetherPortal\manager\PortalManager;
use BeeAZ\AZNetherPortal\manager\PortalTeleporter;
use BeeAZ\AZNetherPortal\listener\EventListener;

class Main extends PluginBase {

    private PortalManager $portalManager;
    private PortalTeleporter $portalTeleporter;

    protected function onEnable(): void {
        $this->saveDefaultConfig();

        $netherName = $this->getConfig()->get("nether_world", "nether");
        $wm = $this->getServer()->getWorldManager();
        if (!$wm->isWorldLoaded($netherName)) {
            if ($wm->isWorldGenerated($netherName)) {
                $wm->loadWorld($netherName);
                $this->getLogger()->info("Loaded Nether world: " . $netherName);
            } else {
                $wm->generateWorld($netherName, WorldCreationOptions::create()->setGeneratorClass(Nether::class));
                $this->getLogger()->info("Generated and loaded new Nether world: " . $netherName);
            }
        }

        $this->portalManager = new PortalManager($this);
        $this->portalTeleporter = new PortalTeleporter($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
            $this->portalTeleporter->tickPortals();
        }), 10);
    }

    public function getPortalManager(): PortalManager {
        return $this->portalManager;
    }

    public function getPortalTeleporter(): PortalTeleporter {
        return $this->portalTeleporter;
    }
}
