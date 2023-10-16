<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Appeal;
use App\Model\Entity\Client;
use App\Model\Entity\User;
use App\Model\Entity\Vendor;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\LocatorAwareTrait;

class AppealMailer extends Mailer
{
	use LocatorAwareTrait;

	/**
	 * Notify vendor user of new appeal assigned
	 *
	 * @param \App\Model\Entity\Appeal $appeal
	 * @param \App\Model\Entity\Vendor $vendor
	 * @param \App\Model\Entity\User $recipient
	 * @return \Cake\Mailer\Mailer
	 */
	public function assignedToVendor(Appeal $appeal, Vendor $vendor, User $recipient): Mailer
	{
		$this->viewBuilder()->setTemplate('vendor_appeal_assigned');

		$this
			->setTo($recipient->email, $recipient->full_name)
			->setSubject(__('Appeal Assigned'))
			->setViewVars([
				'case_id' => $appeal->case->id,
				'appeal_id' => $appeal->id,
				'vendor_name' => $vendor->name,
				'appeal_level_name' => $appeal->appeal_level->name,
				'patient_name' => $appeal->case->patient->full_name,
				'recipient_email' => $recipient->email,
				'recipient_name' => $recipient->full_name,
			])
			->setEmailFormat('both');

		return $this;
	}

	/**
	 * Notify client of appeal completed by vendor
	 *
	 * @param \App\Model\Entity\Appeal $appeal
	 * @param \App\Model\Entity\Client $client
	 * @param \App\Model\Entity\User $recipient
	 * @return \Cake\Mailer\Mailer
	 */
	public function completedByVendor(Appeal $appeal, Client $client, User $recipient): Mailer
	{
		$this->viewBuilder()->setTemplate('vendor_appeal_completed');

		$this
			->setTo($recipient->email, $recipient->full_name)
			->setSubject(__('Appeal Completed'))
			->setViewVars([
				'case_id' => $appeal->case->id,
				'appeal_id' => $appeal->id,
				'client_name' => $client->name,
				'appeal_level_name' => $appeal->appeal_level->name,
				'patient_name' => $appeal->case->patient->full_name,
				'recipient_email' => $recipient->email,
				'recipient_name' => $recipient->full_name,
			])
			->setEmailFormat('both');

		return $this;
	}

	/**
	 * Notify client of appeal returned by vendor
	 *
	 * @param \App\Model\Entity\Appeal $appeal
	 * @param \App\Model\Entity\Client $client
	 * @param \App\Model\Entity\User $recipient
	 * @return \Cake\Mailer\Mailer
	 */
	public function returnedByVendor(Appeal $appeal, Client $client, User $recipient): Mailer
	{
		$this->viewBuilder()->setTemplate('vendor_appeal_returned');

		$this
			->setTo($recipient->email, $recipient->full_name)
			->setSubject(__('Appeal Returned'))
			->setViewVars([
				'case_id' => $appeal->case->id,
				'appeal_id' => $appeal->id,
				'client_name' => $client->name,
				'appeal_level_name' => $appeal->appeal_level->name,
				'patient_name' => $appeal->case->patient->full_name,
				'recipient_email' => $recipient->email,
				'recipient_name' => $recipient->full_name,
			])
			->setEmailFormat('both');

		return $this;
	}
}
