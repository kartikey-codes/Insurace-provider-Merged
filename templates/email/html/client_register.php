<?php

use Cake\Core\Configure;

$this->assign('title', __('Client Register'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __(
	'Client `{0}` has just registered for {1}.',
	$client->name,
	$appName
));

$linkUrl = $this->Url->build([
	'_name' => 'login'
], [
	'fullBase' => true
]);
?>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="mb-2">
	<tbody>
		<tr>
			<td>
				<h1>New Client</h1>
			</td>
		</tr>
		<tr>
			<td>
				<p>Hi <?= $user->first_name ?>,</p>
				<p>A new client, <strong><?= h($client->name) ?></strong>, has just registered for <?= $appName; ?>.</p>
			</td>
		</tr>

		<tr>
			<td>
				<p>
					<span>Owner:</span>

					<?php
					if (!empty($client->owner)) {
					?>
						<strong><?= h($client->owner->full_name) ?></strong>
						<span>(<?= h($client->owner->email) ?>)</span>
					<?php
					} else {
					?>
						<span class="text-danger">Missing</span>
					<?php
					}
					?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
	<tbody>
		<tr>
			<td align="left">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td>
								<a href="<?= $linkUrl; ?>" target="_blank">Log in to <?= $appName ?></a>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
