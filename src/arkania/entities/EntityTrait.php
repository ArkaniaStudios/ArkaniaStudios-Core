<?php

namespace arkania\entities;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;

trait EntityTrait
{

    /** @var bool */
    private bool $isNpc = false;
    
    /** @var string */
    private string $nom = '';
    
    /** @var float|int */
    private float|int $taille = 1;
    
    /** @var array */
    private array $commands = [];
    
    /** @var array */
    private array $inventaire = [];
    
    /** @var float|int */
    private float|int $pitch = 0.0;

    /** @var float|int */
    private float|int $yaw = 0.0;

    /**
     * @return bool
     */
    public function isNpc(): bool {
        return $this->isNpc;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setNpc(bool $value = true): void {
        $this->isNpc = $value;
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function setCustomName(string $name): void {
        $this->nom = $name;
    }

    /**
     * @return string
     */
    public function getCustomName(): string {
        return $this->nom;
    }

    /**
     * @param float $taille
     * @return void
     */
    public function setTaille(float $taille): void {
        $this->taille = $taille;
    }

    /**
     * @return float|int
     */
    public function getTaille(): float|int {
        return $this->taille;
    }

    /**
     * @param array $command
     * @return void
     */
    public function setCommand(array $command): void {
        $this->commands = $command;
    }

    /**
     * @param int $type
     * @param string $command
     * @return void
     */
    public function addCommand(int $type, string $command): void {
        $this->commands[$type][] = $command;
    }

    /**
     * @param string $command
     * @return void
     */
    public function removeCommand(string $command): void {
        foreach ($this->commands as $type => $commands){
            foreach ($commands as $key => $cmd){
                if($cmd === $command){
                    unset($this->commands[$type][$key]);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getCommands(): array {
        return $this->commands;
    }

    /**
     * @return string
     */
    public function listCommands(): string{
        $list = '';
        foreach ($this->commands as $type => $commands){
            foreach ($commands as $key => $cmd){
                $list .= "\n" . "§f- §e" . $cmd . "\n";
            }
        }
        return $list;
    }

    /**
     * @param array $value
     * @return void
     */
    public function setInventory(array $value): void {
        $this->inventaire = $value;
    }

    /**
     * @return array
     */
    public function getEntityInventory(): array {
        return $this->inventaire;
    }

    /**
     * @param float $pitch
     */
    public function setPitch(float $pitch) : void {
        $this->pitch = $pitch;
    }

    /**
     * @return float
     */
    public function getPitch() : float {
        return $this->pitch;
    }

    /**
     * @return float
     */
    public function getYaw() : float {
        return $this->yaw;
    }

    /**
     * @param float $yaw
     */
    public function setYaw(float $yaw) : void {
        $this->yaw = $yaw;
    }

    /**
     * @param CompoundTag $compoundTag
     * @return CompoundTag
     */
    public function saveEntityData(CompoundTag $compoundTag): CompoundTag {
        $compoundTag->setString(EntityDataIds::ENTITY_NAME, $this->getCustomName());
        $compoundTag->setString(EntityDataIds::ENTITY_COMMAND, serialize($this->getCommands()));
        $compoundTag->setString(EntityDataIds::ENTITY_INVENTAIRE, serialize($this->getEntityInventory()));
        $compoundTag->setString(EntityDataIds::ENTITY_NPC, $this->isNpc()? 'true' : 'false');
        $compoundTag->setFloat(EntityDataIds::ENTITY_SIZE, $this->getTaille());
        $compoundTag->setFloat(EntityDataIds::ENTITY_PITCH, $this->getPitch());
        $compoundTag->setFloat(EntityDataIds::ENTITY_YAW, $this->getYaw());
        return $compoundTag;
    }

    /**
     * @param CompoundTag $compoundTag
     * @return void
     */
    public function restorEntityData(CompoundTag $compoundTag): void {
        $this->setNpc();
        $this->setCustomName($compoundTag->getString(EntityDataIds::ENTITY_NAME));
        $this->setTaille($compoundTag->getFloat(EntityDataIds::ENTITY_SIZE));
        $this->setPitch($compoundTag->getFloat(EntityDataIds::ENTITY_PITCH));
        $this->setYaw($compoundTag->getFloat(EntityDataIds::ENTITY_YAW));
        $this->setCommand(unserialize($compoundTag->getString(EntityDataIds::ENTITY_COMMAND, 'a:0:{}')));
        $this->setInventory(unserialize($compoundTag->getString(EntityDataIds::ENTITY_INVENTAIRE, 'a:0:{}')));
    }

    /**
     * @param string $command
     * @return bool
     */
    public function hasCommands(string $command) : bool {
        foreach ($this->commands as $type => $commands) {
            foreach ($commands as $key => $cmd) {
                if($cmd === $command) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function executeCommand(Player $player): void {
        $playersCommand = $this->getCommands()[0] ?? [];
        $serverCommands = $this->getCommands()[1] ?? [];

        $serverInstance = Server::getInstance();
        foreach ($playersCommand as $command){
            $serverInstance->dispatchCommand($player, $command);
        }

        foreach ($serverCommands as $command){
            $serverInstance->dispatchCommand(new ConsoleCommandSender($serverInstance, $serverInstance->getLanguage()), str_replace('{playername}', $player->getName(), $command));
        }
    }

}