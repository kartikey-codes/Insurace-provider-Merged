<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
$companyName = Configure::readOrFail('Company.name');

$urlOptions = [
	'fullBase' => true
];

?>
<!DOCTYPE html>
<html lang="en" class="w-auto h-100">

<head>
	<meta charset="utf-8" />
	<title><?= $appName ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php if (!empty($_sslUpgradeInsecure)) { ?>
		<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<?php } ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Content-Language" content="en-US">
	<meta name="author" content="<?= $companyName ?>">
	<meta name="copyright" content="Copyright <?= date('Y'); ?> <?= $companyName ?>">
	<meta name="robots" content="NOINDEX,NOFOLLOW">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="format-detection" content="telephone=yes">
	<meta name="theme-color" content="#dc3545">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?= $appName ?>">
	<meta name="mobile-web-app-capable" content="yes">
	<?php
	echo $this->element('icons');

	echo $this->Html->meta([
		'link' => 'mix-manifest.json',
		'rel' => 'manifest'
	]);

	echo $this->fetch('meta');
	echo $this->Mix->css('error.css');
	echo $this->fetch('css');

	echo $this->Html->meta(
		'favicon.ico',
		'/favicon.ico',
		['type' => 'icon']
	);
	?>
	<style type="text/css">
		[v-cloak] {
			display: none !important;
		}
	</style>
</head>

<body class="w-auto h-100 bg-danger text-white text-center d-flex align-items-center justify-content-center">
	<div id="container" class="error text-center">
		<div id="header">
			<h1 class="font-weight-bold"><?= __('Error') ?></h1>
		</div>
		<div id="content" class="text-break">
			<?= $this->fetch('content') ?>
		</div>
	</div>
</body>

</html>
