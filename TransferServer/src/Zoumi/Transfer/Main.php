<?php

namespace Zoumi\Transfer;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Zoumi\Lobby\api\libpmquery\PMQuery;
use Zoumi\Lobby\api\libpmquery\PmQueryException;

class Main extends PluginBase implements Listener {

    public Config $config;

    public static $instance;

    public static function getIntstance(): self{
        return self::$instance;
    }

    public function onEnable()
    {
        $this->getLogger()->info("plugin enabled.");
        self::$instance = $this;
        $this->setupFile();
        $this->config = new Config($this->getDataFolder() . "servers.json", Config::JSON);
        $this->getServer()->getCommandMap()->register("FastTransfer", new Server("server", "Ouvrir l'ui du FastTransfer.", "", []));
    }

    public function setupFile()
    {
        if (!file_exists($this->getDataFolder() . "servers.json")){
            $this->saveResource("servers.json");
        }
    }

}