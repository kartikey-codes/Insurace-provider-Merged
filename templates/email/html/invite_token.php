<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$this->assign('title', __('Join {0} on {1}', $token->inviter->organization, $appName));

$this->assign('preheader', __(
	'You\'ve been invited to join {0} on {1} by {2}.',
	$token->inviter->organization,
	$appName,
	$token->inviter->name
));

$linkUrl = $this->Url->build([
	'_name' => 'inviteTokenRedeem',
	'?' => [
		'token' => $token->getToken()
	]
], [
	'fullBase' => true
]);
?>
<h1>Join <?= $token->inviter->organization ?></h1>

<p class="mb-2">
	You've been invited to join <strong><?= $token->inviter->organization ?></strong> on <strong><?= $appName ?></strong> by <?= $token->inviter->name ?>.
</p>

<p class="mb-2">
	Click the link below to either register a new account or log in and join your organization.
</p>

<?php
if (!empty($tempPassword)) {
?>
	<p>
		You have been assigned a temporary password that you may change upon logging in.
	</p>
	<p class="mb-2">
		<strong>Your temporary password is:</strong>
		<span><?= $tempPassword; ?></span>
	</p>
<?php
}
?>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary mb-2">
	<tbody>
		<tr>
			<td align="left">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td> <a href="<?= $linkUrl; ?>" target="_blank">Open <?= $appName ?></a> </td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<p>
	Thanks again for using <?= $appName; ?>!
</p>
