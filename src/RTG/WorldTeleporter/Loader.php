<?php

/* 
 * Copyright (C) 2017 RTG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace RTG\WorldTeleporter;

/* Essentials */
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Loader extends pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onTP($world, Player $p) {
        
        if($this->getServer()->loadLevel($world) != false) {
            $p->teleport(Server::getInstance()->getLevelByName($world)->getSafeSpawn());
            $p->sendMessage("You've been teleported to $world!");
        }
        else {
            $this->getServer()->loadLevel($world);
            $p->teleport(Server::getInstance()->getLevelByName($world)->getSafeSpawn());
            $p->sendMessage("You've been teleported to $world!");
        }
        
    }
    
    public function onSign(SignChangeEvent $e) {
        
        $p = $e->getPlayer();
            
            if($e->getLine(1) === "MOVE" || $e->getLine(1) === "[MOVE]") {
                
                if($p->hasPermission("worldteleporter.create")) {
                    
                    if(is_numeric($e->getLine(2))) {
                        $e->setCancelled();
                        $p->sendMessage("Line 2 has to be the world name! Integer given.");
                    }
                    else {
                        
                        $world = $e->getLine(2);
                        $this->onTP($world, $p);
                              
                    }
                     
                }
                else {
                    $p->sendMessage(TF::RED . "You have no permission to use this feature!");
                    $e->setCancelled();
                }
                 
            }
        
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        
        switch(strtolower($command->getName())) {
            
            case "wtp":
                
                if($sender->hasPermission("worldteleporter.command")) {
                    
                    if(isset($args[0])) {
                        
                        if(isset($args[1])) {
                            
                            $this->onTP($args[0], $args[1]);
                              
                        }
                        else {
                            $sender->sendMessage(TF::RED . "/wtp [world] [player]");
                        }
                        
                    }
                    else {
                        $sender->sendMessage(TF::RED . "/wtp [world] [player]");
                    }
                     
                }
                else {
                    $sender->sendMessage(TF::RED . "You have no permission to use this command!");
                }
                
                return true;
                
        }
        
    }
    
    
}