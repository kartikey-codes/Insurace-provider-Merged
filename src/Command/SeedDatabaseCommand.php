<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Migrations\Migrations;

class SeedDatabaseCommand extends Command
{
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
		$migrations = new Migrations();

		$seed = 'DefaultDatabaseSeed';

		if ($migrations->seed(['seed' => $seed])) {
			$io->out(__('Completed seeding database!'));
		} else {
			$io->err(__('Failed to seed database.'));
		}
	}
}
