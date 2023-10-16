<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\NpiServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class NpiNumberLookupCommand extends Command
{
	/**
     * @var \App\Service\NpiServiceInterface
     */
	public NpiServiceInterface $npiService;

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
	 * @throws \LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		$parser->addArgument('number', [
			'help' => 'The NPI number to look up.',
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
		$number = intval($args->getArgument('number'));

		if (empty($number)) {
			$io->abort(__('An NPI number must be provided as an argument.'));
		}

		$start = microtime(true);
		$result = $this->npiService->searchIndividualByNumber($number);
		$end = microtime(true) - $start;

		$io->info(__('Search completed in {0} seconds.', $end));

		if (empty($result)) {
			$io->info(__('No results for {0} were found', $number));

			return;
		} else {
			$io->info(__('Found {0} results', $result['result_count']));
		}

		foreach ($result['results'] as $entity) {
			$io->out(sprintf(
				'%s with taxonomies: %s',
				$entity['basic']['organization_name'],
				implode(', ', array_column($entity['taxonomies'], 'desc'))
			));
		}
	}
}
