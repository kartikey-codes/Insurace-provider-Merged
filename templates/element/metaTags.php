<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
$companyName = Configure::readOrFail('Company.name');
$sslUpgradeInsecure = Configure::readOrFail('SSL.upgradeInsecureRequests');

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if (!empty($sslUpgradeInsecure)) { ?>
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<?php } ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Language" content="en-US">
<meta name="author" content="<?= $companyName ?>">
<meta name="copyright" content="Copyright <?= date('Y'); ?> <?= $companyName ?>">
<meta name="robots" content="NOINDEX,NOFOLLOW">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
<meta name="format-detection" content="telephone=yes">
<meta name="apple-mobile-web-app-status-bar-style" content="#FFFFFF">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= $appName; ?>">
<meta name="mobile-web-app-capable" content="yes">
