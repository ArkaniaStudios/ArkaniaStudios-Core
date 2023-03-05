<?php

declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 *
 */

namespace arkania\entity;

use arkania\Core;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;

trait EntityTrait {

    private Core $core;

    public function __construct() {
        $this->core = Core::getInstance();
    }

    /** @var bool  */
    private bool $npc = false;

    /** @var string  */
    private string $name = '';

    /** @var array  */
    public array $commands = [];

    /** @var float  */
    private float $taille = 1;

    /**
     * @param bool $value
     * @return void
     */
    public function setNpc(bool $value = true): void {
        $this->npc = $value;
    }

    /**
     * @return bool
     */
    public function isNpc(): bool {
        return $this->npc;
    }

    /**
     * @param array $command
     * @return void
     */
    public function setCommand(array $command): void {
        $this->commands = $command;
    }

    /**
     * @param string $commandName
     * @return void
     */
    public function addCommand(string $commandName): void{
        $this->commands[] = $commandName;
    }

    /**
     * @param int $command
     * @return void
     */
    public function removeCommand(int $command): void {
        unset($this->commands[$command]);
        var_dump($this->commands[$command]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function executeCommand(Player $player): void {
        foreach ($this->getCommand() as $commands)
            Server::getInstance()->dispatchCommand($player, $commands);
    }

    /**
     * @return array
     */
    public function getCommand(): array {
        return $this->commands;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setCustomName(string $name): void {
        $this->name = $name;
        $this->setNameTag($name);
        $this->setNameTagAlwaysVisible();
        $this->setNameTagVisible();
    }

    public function getCustomName(): string {
        return $this->name;
    }

    /**
     * @param float $size
     * @return void
     */
    public function setTaille(float $size): void {
        $this->taille = $size;
        $this->setScale($size);
    }

    /**
     * @return float
     */
    public function getTaille(): float{
        return $this->taille;
    }

    /**
     * @param CompoundTag $compoundTag
     * @return CompoundTag
     */
    public function saveNpcData(CompoundTag $compoundTag): CompoundTag {

        $compoundTag->setInt(EntityIds::NPC, (int)$this->isNpc());
        $compoundTag->setString(EntityIds::NAME, $this->getCustomName());
        $compoundTag->setString(EntityIds::COMMAND, serialize($this->getCommand()));
        $compoundTag->setFloat(EntityIds::SIZE, $this->getTaille());

        return $compoundTag;
    }

    /**
     * @param CompoundTag $compoundTag
     * @return void
     */
    public function restorNpcData(CompoundTag $compoundTag): void {

        $this->setNpc();
        $this->setCustomName($compoundTag->getTag(EntityIds::NAME)->getValue());
        $this->setCommand(unserialize($compoundTag->getTag(EntityIds::COMMAND)->getValue()));
        $this->setTaille($compoundTag->getTag(EntityIds::SIZE)->getValue());
    }
}