<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class PasswordHashCommand extends Command
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
		$parser->addArgument('password', [
			'help' => 'The password to hash using the default password hasher.',
		]);

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
		$password = $args->getArgument('password');

		if (empty($password)) {
			$io->abort(__('A password must be provided as an argument.'));
		}

		$hasher = new DefaultPasswordHasher();
		$hash = $hasher->hash($password);

		$io->out($hash, 0);
	}
}
