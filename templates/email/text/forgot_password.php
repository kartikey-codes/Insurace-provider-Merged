<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$link = [
	'controller' => 'Auth',
	'action' => 'resetPassword',
	$user->password_reset_token
];
?>
Hi <?= $user->first_name ?>,

You have requested your password to be reset for <?= $appName ?>.

If you did not request your password to be reset, you can ignore this message. Your password will remain unchanged.

The URL below contains a token for resetting your password:

<?= $this->Url->build($link, ['fullBase' => true]); ?>
