<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title><?= $_appName ?></title>
	<?= $this->element('metaTags') ?>
	<?= $this->element('icons') ?>
	<meta name="api-base-url" content="<?= $this->Url->build('/vendor/api', ['fullBase' => true]) ?>">
	<meta name="csrf-token" content="<?= $this->getRequest()->getParam('_csrfToken') ?>">
	<meta name="api-token" content="<?= $_apiToken ?>">
	<?php
	echo $this->Html->meta([
		'link' => 'mix-manifest.json',
		'rel' => 'manifest'
	]);

	echo $this->fetch('meta');
	echo $this->Mix->css('vendors.css');
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

<body class="spa">
	<app id="app"></app>
	<script type="text/javascript">
		window.appConfig = <?= json_encode($_appConfig); ?>;
		window.appName = <?= json_encode($_appName); ?>;
		window.authUser = <?= json_encode($_user); ?>;
	</script>
	<?php
	echo $this->fetch('afterContent');
	echo $this->Mix->js('manifest.js');

	// Load minified build when Cake's Debug mode is false.
	if (!empty($_debugMode) && $_debugMode == true) {
		echo $this->Mix->js('vendor.js');
		echo $this->Mix->js('vendors.js');
	} else {
		echo $this->Mix->js('vendor.min.js');
		echo $this->Mix->js('vendors.min.js');
	}

	echo $this->fetch('script');
	?>
</body>

</html>
