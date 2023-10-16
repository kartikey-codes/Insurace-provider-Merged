<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use InvalidArgumentException;
use Cake\Datasource\Exception\MissingModelException;
use Migrations\Migrations;
use UnexpectedValueException;

class RunAllMigrationsCommand extends Command
{
	/**
	 * @var Migrations
	 */
	private Migrations $migrations;

	/**
	 * Constructor
	 * @return void
	 * @throws InvalidArgumentException
	 * @throws MissingModelException
	 * @throws UnexpectedValueException
	 */
	public function __construct()
	{
		parent::__construct();

		$this->migrations = new Migrations();
	}

	/**
	 * Build Options
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 * @throws \App\Command\LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		return $parser;
	}

	/**
	 * Execute
	 *
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return void
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Console\Exception\StopException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		if ($this->runPluginMigrations()) {
			$io->success(__('Completed migrations for DatabaseLog plugin.'));
		} else {
			$io->err(__('Failed to run DatabaseLog plugin migrations.'));
		}

		if ($this->runAppMigrations()) {
			$io->success(__('Completed migrating app database.'));
		} else {
			$io->err(__('Failed to run application migrations.'));
		}
	}

	/**
	 * Run plugins first
	 * @return bool
	 */
	private function runPluginMigrations(): bool
	{
		return $this->migrations->migrate(['plugin' => 'DatabaseLog']);
	}

	/**
	 * Run application second
	 * @return bool
	 */
	private function runAppMigrations(): bool
	{
		return $this->migrations->migrate([]);
	}
}
