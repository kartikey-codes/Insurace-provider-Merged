<?php

declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Exception;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class MixHelper extends Helper
{
	/**
	 * @var array<string>
	 */
	public $helpers = [
		'Html',
	];

	/**
	 * @var array
	 */
	protected array $manifest = [];

	/**
	 * Initialize
	 *
	 * @param array $config Options
	 * @return void
	 */
	public function initialize(array $config = []): void
	{
		$manifestPath = WWW_ROOT . 'mix-manifest.json';

		$this->setConfig($config + [
			'path' => $manifestPath,
		]);

		$this->manifest = $this->readManifest();
	}

	/**
	 * Read manifest into object
	 *
	 * @return array
	 */
	protected function readManifest(): array
	{
		if (!file_exists($this->getConfig('path'))) {
			return [];
		}

		$contents = file_get_contents($this->getConfig('path'));

		return json_decode($contents, true);
	}

	/**
	 * Output versioned CSS tag
	 *
	 * @param string $file File name
	 * @param array $options
	 * @return ?string
	 * @throws \Exception
	 */
	public function css(string $file, array $options = []): ?string
	{
		if (empty($this->manifest['/css/' . $file])) {
			throw new Exception(__('Could not find CSS file: {0}', $file));
		}

		if (!empty($options['string'])) {
			return $this->manifest['/css/' . $file];
		}

		return $this->Html->css(
			$this->manifest['/css/' . $file],
			$options
		);
	}

	/**
	 * Output versioned javascript tag
	 *
	 * @param string $file File name
	 * @param array $options
	 * @return ?string
	 * @throws \Exception
	 */
	public function js(string $file, array $options = []): ?string
	{
		if (empty($this->manifest['/js/' . $file])) {
			throw new Exception(__('Could not find JS file: {0}', $file));
		}

		if (!empty($options['string'])) {
			return $this->manifest['/js/' . $file];
		}

		return $this->Html->script(
			$this->manifest['/js/' . $file],
			$options
		);
	}

	/**
	 * Output versioned image tag
	 *
	 * @param string $file File name
	 * @param array $options
	 * @return string
	 * @throws \Exception
	 */
	public function img(string $file, array $options = []): string
	{
		if (empty($this->manifest['/img/' . $file])) {
			throw new Exception(__('Could not find image file: {0}', $file));
		}

		if (!empty($options['string'])) {
			return $this->manifest['/img/' . $file];
		}

		return $this->Html->image(
			$this->manifest['/img/' . $file],
			$options
		);
	}
}
