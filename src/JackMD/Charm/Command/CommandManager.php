<?php
declare(strict_types = 1);

namespace JackMD\Charm\Command;

use JackMD\Charm\Charm;
use JackMD\Charm\Command\Defaults\Broadcast;
use JackMD\Charm\Command\Defaults\ClearInventory;
use JackMD\Charm\Command\Defaults\Compass;
use JackMD\Charm\Command\Defaults\Feed;
use JackMD\Charm\Command\Defaults\Fly;
use JackMD\Charm\Command\Defaults\Gamemode;
use JackMD\Charm\Command\Defaults\Heal;
use JackMD\Charm\Command\Defaults\KickAll;
use JackMD\Charm\Command\Defaults\Ping;
use JackMD\Charm\Command\Defaults\Spawn;
use JackMD\Charm\Command\Defaults\TeleportAll;
use JackMD\Charm\Command\Defaults\TeleportHere;
use JackMD\Charm\Command\Defaults\Top;
use pocketmine\command\Command;
use function in_array;
use function is_null;

class CommandManager{

	/** @var Charm */
	private $plugin;

	/** @var array */
	private $overrideableCommands = [
		"gamemode"
	];

	/**
	 * CommandManager constructor.
	 *
	 * @param Charm $plugin
	 */
	public function __construct(Charm $plugin){
		$this->plugin = $plugin;

		$this->registerAll();
	}

	/**
	 * Registers all the commands.
	 */
	private function registerAll(): void{
		$plugin = $this->plugin;

		/** @var BaseCommand[] $commands */
		$commands = [
			new Gamemode($plugin),
			new TeleportAll($plugin),
			new TeleportHere($plugin),
			new Compass($plugin),
			new Feed($plugin),
			new Fly($plugin),
			new Heal($plugin),
			new KickAll($plugin),
			new Ping($plugin),
			new Spawn($plugin),
			new Top($plugin),
			new ClearInventory($plugin),
			new Broadcast($plugin)
		];

		foreach($commands as $command){
			$this->registerCommand($command);
		}
	}

	/**
	 * @param Command $command
	 */
	public function registerCommand(Command $command): void{
		$plugin = $this->plugin;
		$commandName = $command->getName();

		if((bool) !$plugin->getConfig()->getNested("commands.$commandName")){
			return;
		}

		if(in_array($commandName, $this->overrideableCommands)){
			$this->unregisterCommand($commandName);
		}

		$plugin->getServer()->getCommandMap()->register("charm", $command);
		$plugin->getServer()->getLogger()->debug(Charm::PREFIX . "§7Registered Command: §6$commandName");
	}

	/**
	 * @param string $commandName
	 */
	public function unregisterCommand(string $commandName): void{
		$commandMap = $this->plugin->getServer()->getCommandMap();

		if(!is_null($command = $commandMap->getCommand($commandName))){
			$commandMap->unregister($command);
		}
	}
}