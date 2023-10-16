<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Model\Entity\Integration;

/**
 * Integrations Controller
 */
class IntegrationsController extends ApiController
{
	/**
	 * All method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$data = [];
		$profiles = $this->Integrations->find()->all();

		foreach ($profiles as $profile) {
			switch ($profile->integration_name) {
				case Integration::MIRTH_CONNECT:
					$data[Integration::MIRTH_CONNECT] = $profile;
					break;
				default:
					// Unsupported integration
					break;
			}
		}

		$this->set('data', [
			'profiles' => $profiles,
			...$data
		]);
	}

	/**
	 * Update method
	 *
	 * @return void
	 */
	public function update(): void
	{
		$this->request->allowMethod(['post']);

		$name = $this->getRequest()->getData('name', null);
		$description = $this->getRequest()->getData('description', null);
		$config = $this->getRequest()->getData('config', []);

		// Find existing and validate the $name parameter
		$profiles = $this->Integrations
			->find('byName', [
				'name' => $name
			])
			->all();

		if ($profiles->isEmpty()) {
			$integration = $this->createConfig($name, $config, $description);
		} else {
			$integration = $this->updateConfig($name, $config, $description);
		}

		$this->set('data', $integration);
	}

	/**
	 * Create a integration profile in the database
	 *
	 * @param string $name
	 * @param array $configuration
	 * @param string $description
	 * @return Integration
	 */
	private function createConfig(string $name, array $configuration, string $description = null): Integration
	{
		$entity = $this->Integrations->newEntity([
			'integration_name' => $name,
			'config_json' => json_encode($configuration),
			'description' => $description
		]);

		return $this->Integrations->saveOrFail($entity);
	}

	/**
	 * Update a integration profile in the database
	 *
	 * @param string $name
	 * @param array $config
	 * @param string $description
	 * @return Integration
	 */
	private function updateConfig(string $name, array $config, string $description = null): Integration
	{
		// Only one config per type for now
		$existing = $this->Integrations
			->find('byName', [
				'name' => $name
			])
			->firstOrFail();

		$data = $this->Integrations->patchEntity($existing, [
			'config_json' => json_encode($config),
			'description' => $description
		], [
			'fields' => [
				'config_json',
				'description'
			]
		]);

		return $this->Integrations->saveOrFail($data);
	}
}
