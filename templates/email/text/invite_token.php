<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$linkUrl = $this->Url->build([
	'_name' => 'inviteTokenRedeem',
	'?' => [
		'token' => $token->getToken()
	]
], [
	'fullBase' => true
]);
?>
Hello,

You've been invited to join <?= $token->inviter->organization ?> on <?= $appName ?> by <?= $token->inviter->name ?>.

The URL below can be used to join your organization:

<?= $this->Url->build($linkUrl, ['fullBase' => true]); ?>
