<?php

/**
 * CakePDF Plugin Configuration
 */
return [
	'CakePdf' => [
		'engine' => [
			'className' => 'CakePdf.DomPdf',
			'options' => [
				//'compress' => false,
				//'defaultPaperSize' => 'letter',
				//'defaultMediaType' => 'print',
				//'fontHeightRatio' => 1.1,
				'chroot' => WWW_ROOT,
				//'dpi' => 96,
				//'pdfBackend' => 'auto' // default: CPDF
			]
		],
		'orientation' => 'portrait',
		// 'pageSize' => 'letter', // A4
		'margin' => [
			'bottom'    => 0,
			'left'      => 0,
			'right'     => 10,
			'top'       => 0
		]
	]
];
