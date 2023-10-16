<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\AppealNote;
use App\Model\Entity\Client;
use App\Model\Entity\User;
use App\Model\Entity\Vendor;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\LocatorAwareTrait;

class AppealNoteMailer extends Mailer
{
	use LocatorAwareTrait;

	/**
	 * Notify client user of new appeal note added
	 *
	 * @param \App\Model\Entity\AppealNote $appealNote
	 * @param \App\Model\Entity\User $createdByUser
	 * @param \App\Model\Entity\Client $client
	 * @param \App\Model\Entity\User $recipient
	 * @return \Cake\Mailer\Mailer
	 */
	public function notifyClient(AppealNote $appealNote, User $createdByUser, Client $client, User $recipient): Mailer
	{
		$this->viewBuilder()->setTemplate('new_appeal_note');

		$vars = $this->getViewVariables($appealNote, $createdByUser, $recipient);
		$vars += [
			'notify' => 'client',
			'name' => $client->name,
		];

		$this
			->setTo($recipient->email, $recipient->full_name)
			->setSubject(__('New Appeal Note'))
			->setViewVars($vars)
			->setEmailFormat('both');

		return $this;
	}

	/**
	 * Notify vendor user of new appeal note added
	 *
	 * @param \App\Model\Entity\AppealNote $appealNote
	 * @param \App\Model\Entity\User $createdByUser
	 * @param \App\Model\Entity\Vendor $vendor
	 * @return \Cake\Mailer\Mailer
	 */
	public function notifyVendor(AppealNote $appealNote, User $createdByUser, Vendor $vendor, User $recipient): Mailer
	{
		$this->viewBuilder()->setTemplate('new_appeal_note');

		$vars = $this->getViewVariables($appealNote, $createdByUser, $recipient);
		$vars += [
			'notify' => 'client',
			'name' => $vendor->name,
		];

		$this
			->setTo($recipient->email, $recipient->full_name)
			->setSubject(__('New Appeal Note'))
			->setViewVars($vars)
			->setEmailFormat('both');

		return $this;
	}

	/**
	 * Get view variables that are the same regardless of client/vendor
	 *
	 * @param \App\Model\Entity\AppealNote $appealNote
	 * @param \App\Model\Entity\User $createdByUser
	 * @param \App\Model\Entity\User $recipient
	 * @return array
	 */
	private function getViewVariables(AppealNote $appealNote, User $createdByUser, User $recipient): array
	{
		return [
			'note_text' => $appealNote->notes,
			'case_id' => $appealNote->appeal->case->id,
			'appeal_id' => $appealNote->appeal_id,
			'appeal_level_name' => $appealNote->appeal->appeal_level->name,
			'patient_name' => $appealNote->appeal->case->patient->full_name,
			'created_by_user_name' => $createdByUser->full_name,
			'created_by_user_type' => $this->getTypeOfUser($createdByUser),
			'recipient_email' => $recipient->email,
			'recipient_name' => $recipient->full_name,
		];
	}

	/**
	 * Get the type of user that created this appeal note
	 * so the email template can adjust accordingly
	 *
	 * @param \App\Model\Entity\User $user
	 * @return string
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \RuntimeException
	 */
	private function getTypeOfUser(User $user)
	{
		if ($user->isAdmin()) {
			return 'admin';
		} elseif ($user->isVendorUser()) {
			return 'vendor';
		} elseif ($user->isClientUser()) {
			return 'client';
		} else {
			throw new \RuntimeException(__('Could not determine type of user that created appeal note.'));
		}
	}
}
