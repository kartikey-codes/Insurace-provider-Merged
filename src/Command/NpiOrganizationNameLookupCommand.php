<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\NpiServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use InvalidArgumentException;
use Cake\Datasource\Exception\MissingModelException;
use UnexpectedValueException;

class NpiOrganizationNameLookupCommand extends Command
{
	/**
	 * @var \App\Service\NpiServiceInterface
	 */
	public NpiServiceInterface $npiService;

	/**
	 * Constructor
	 *
	 * @param NpiServiceInterface $npiService
	 * @return void
	 * @throws InvalidArgumentException
	 * @throws MissingModelException
	 * @throws UnexpectedValueException
	 */
	public function __construct(NpiServiceInterface $npiService)
	{
		parent::__construct();

		$this->npiService = $npiService;
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
		$parser->addArgument('name', [
			'help' => 'The organization name to look up.',
			'required' => true,
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
		$name = $args->getArgument('name');

		if (empty($name)) {
			$io->abort(__('An organization name must be provided as an argument.'));
		}

		$start = microtime(true);
		$result = $this->npiService->searchOrganizationByName($name);
		$end = microtime(true) - $start;

		$io->info(__('Search completed in {0} seconds.', $end));

		if (empty($result)) {
			$io->info(__('No results for {0} were found', $name));

			return;
		} else {
			$io->info(__('Found {0} results', $result['result_count']));
		}

		foreach ($result['results'] as $entity) {
			$io->out(sprintf(
				'#%s: %s ----- Taxonomies: %s',
				$entity['number'],
				$entity['basic']['organization_name'],
				implode(', ', array_column($entity['taxonomies'], 'desc'))
			));
		}
	}
}
