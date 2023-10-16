<?php

/**
 * PDF Layout
 *
 * Rendered by DomPdf
 */
?>
<html>

<head>
	<title><?= $this->fetch('title') ?></title>
	<link type="text/css" media="dompdf" href="<?= WWW_ROOT . 'css' . DS . 'pdf.css' ?>" rel="stylesheet" />
</head>

<body><?= $this->fetch('content'); ?></body>

</html>
