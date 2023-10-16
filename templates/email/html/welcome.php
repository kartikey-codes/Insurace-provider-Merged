<?php

use Cake\Core\Configure;

$this->assign('title', __('Welcome'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __(
	'Welcome, {0} to {1}!',
	$user->full_name,
	$appName
));

$linkUrl = $this->Url->build([
	'_name' => 'login'
], [
	'fullBase' => true
]);
?>
<h1>Welcome, <?= $user->first_name ?>!</h1>
<p class="mb-2">Your account has been created on <?= $appName ?>.</p>

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
							<td> <a href="<?= $linkUrl; ?>" target="_blank">Log in to <?= $appName ?></a> </td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<p class="mb-4">
	Thanks again for joining <?= $appName; ?>!
</p>
<p class="text-muted">
	If you did not request an account, your organization may have registered an account for you. Please contact support if you believe this is an error.
</p>
