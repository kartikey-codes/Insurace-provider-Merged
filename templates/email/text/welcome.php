<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$link = ['_name' => 'login'];

$hasTempPassword = empty($tempPassword) ? false : true;
?>
Welcome, <?= $user->first_name ?>!

Your account has been created on <?= $appName ?>.

<?php
if ($hasTempPassword) {
?>
	You have been assigned a temporary password that you may change upon logging in.

	Your temporary password is: <?= $tempPassword ?>
<?php
}
?>

You can sign in to your dashboard at the URL below:
<?= $this->Url->build($link, ['fullBase' => true]); ?>

If you did not request an account, your organization may have registered an account for you.
Please contact support if you believe this is an error.
