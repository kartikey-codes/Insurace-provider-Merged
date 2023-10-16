<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
$companyName = Configure::readOrFail('Company.name');
$logo = Configure::readOrFail('Login.logo');
$bgImage = Configure::readOrFail('Login.backgroundImage');
$loginBg = $this->Url->build('/img/' . $bgImage, [
	'fullBase' => true
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->Html->charset() ?>
	<title><?= $this->fetch('title') . ' | ' . $appName ?></title>
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
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?= $appName; ?>">
	<meta name="mobile-web-app-capable" content="yes">
	<?php
	echo $this->element('icons');

	echo $this->Html->meta([
		'link' => 'mix-manifest.json',
		'rel' => 'manifest'
	]);

	echo $this->fetch('meta');
	echo $this->fetch('css');

	echo $this->Mix->css('login.css');

	echo $this->Html->meta(
		'favicon.ico',
		'/favicon.ico',
		['type' => 'icon']
	);
	?>
	<link rel="prefetch" href="<?= $this->Mix->css('clients.css', ['string' => true]) ?>" as="style">
	<link rel="prefetch" href="<?= $this->Mix->js('clients.js', ['string' => true]) ?>" as="script">
	<link rel="prefetch" href="<?= $this->Mix->js('vendors.js', ['string' => true]) ?>" as="script">
</head>

<body class="antialiased bg-cover" style="background-image: url(<?= $loginBg ?>)">
	<div class="container">
		<div class="row">
			<div class="col-8 offset-2 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 col-xl-4 offset-xl-4 mt-xl-5 mb-xl-4">
				<div class="text-center mx-auto mt-4 mt-lg-5 mb-4">
					<?php
					echo $this->Html->image($logo, [
						'alt' => $appName,
						'class' => 'img-fluid img-responsive',
						'draggable' => 'false' // doesn't support booleans
					]);
					?>
				</div>
			</div>
		</div>
	</div>
	<main><?= $this->fetch('content') ?></main>
	<?php
	echo $this->Mix->js('manifest.js');
	echo $this->Mix->js('vendor.js');
	echo $this->Mix->js('login.js');
	echo $this->fetch('script');
	?>
</body>

</html>
