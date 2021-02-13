<?php

namespace Zoumi\Transfer;

use FormAPI\CustomForm;
use FormAPI\SimpleForm;
use libpmquery\PMQuery;
use libpmquery\PmQueryException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Server extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            $this->openServer($sender);
        }
    }

    public function openServer(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    $this->openCreateServer($player);
                    break;
                case 1:
                    $this->removeServer($player);
                    break;
                case 2:
                    $this->joinServer($player);
                    break;
            }
        });
        $ui->setTitle("§7- §3Serveur §7-");
        $ui->addButton("Ajouter un serveur");
        $ui->addButton("Supprimer un serveur");
        $ui->addButton("Rejoindre un serveur");
        $ui->sendToPlayer($player);
    }

    public function openCreateServer(Player $player){
        $ui = new CustomForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            if (is_null($data[0])){
                $player->sendMessage("§f(§7FastTransfer§f) Vous devez entré une adresse !");
                return;
            }elseif (is_null($data[1])){
                if (is_null($data[2])){
                    $config = Main::getIntstance()->config;
                    $config->set($data[0], $data[0] . ":" . "19132");
                    $config->save();
                    $player->sendMessage("§f(§7FastTransfer§f) Le serveur §e" . $data[0] . " §fa été ajouter avec succès.");
                    return;
                }else{
                    $config = Main::getIntstance()->config;
                    $config->set($data[2], $data[0] . ":" . "19132");
                    $config->save();
                    $player->sendMessage("§f(§7FastTransfer§f) Le serveur §e" . $data[2] . " §fa été ajouter avec succès.");
                    return;
                }
            }else{
                if (is_null($data[2])){
                    $config = Main::getIntstance()->config;
                    $config->set($data[0], $data[0] . ":" . $data[1]);
                    $config->save();
                    $player->sendMessage("§f(§7FastTransfer§f) Le serveur §e" . $data[0] . " §fa été ajouter avec succès.");
                    return;
                }else{
                    $config = Main::getIntstance()->config;
                    $config->set($data[2], $data[0] . ":" . $data[1]);
                    $config->save();
                    $player->sendMessage("§f(§7FastTransfer§f) Le serveur §e" . $data[2] . " §fa été ajouter avec succès.");
                    return;
                }
            }
        });
        $ui->setTitle("§7- §3Ajouter un serveur §7-");
        $ui->addInput("§7- §fVeuillez entré l'ip du serveur ci-dessous:", "moonlight-mc.eu");
        $ui->addInput("§7- §fVeuillez saisir le port du serveur ci-dessous:", "19132");
        $ui->addInput("§7- §fQuel nom voulez-vous lui donné ?", "Moonlight");
        $ui->sendToPlayer($player);
    }

    public function removeServer(Player $player){
        $ui = new CustomForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            if (!is_null($data[0])){
                $config = Main::getIntstance()->config;
                if ($config->exists($data[0])){
                    $config->remove($data[0]);
                    $config->save();
                    $player->sendMessage("§f(§7FastTransfer§f) Le serveur §e" . $data[0] . " §fa bien été supprimer.");
                    return;
                }else{
                    $player->sendMessage("§f(§7FastTransfer§f) Ce serveur n'existe pas.");
                    return;
                }
            }else{
                $player->sendMessage("§f(§7FastTransfer§f) Vous devez saisir le nom du serveur.");
                return;
            }
        });
        $ui->setTitle("§7- §3Supprimer un serveur §7-");
        $ui->addInput("§7- §fVeuillez saisir le nom du serveur que vous souhaitez supprimer.");
        $ui->sendToPlayer($player);
    }

    public function joinServer(Player  $player){
        $ui = new SimpleForm(function (Player  $player, $data){
            if ($data === null){
                return;
            }
            $config = Main::getIntstance()->config;
            $int = 0;
            $s = [];
            foreach ($config->getAll() as $serverName => $adresse){
                $s[$int] = $serverName;
                $int++;
            }
            $serv = explode(":", $config->get($s[$data]));
            $player->transfer($serv[0], $serv[1]);
        });
        $ui->setTitle("§7- §3Rejoindre un serveur §7-");
        $config = Main::getIntstance()->config;
        foreach ($config->getAll() as $serverName => $adresse){
            $serv = explode(":", $adresse);
            try {
                PMQuery::query($serv[0], $serv[1]);
                $ui->addButton($serverName . "\n§aONLINE");
            }catch (PmQueryException $exception){
                $ui->addButton($serverName . "\n§cOFFLINE");
            }
        }
        $ui->sendToPlayer($player);
    }

}