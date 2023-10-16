<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$this->assign('title', __('{0} Terms of Service & Agreement', $appName));

// Version is stored in app configuration
$versionDate = Configure::read('TermsOfService.vendorDate');
?>
<div class="container">
	<div class="row mb-4">
		<div class="col-12">
			<div class="bg-white card p-4 shadow-sm rounded-lg">
				<div class="row">
					<div class="col-sm-12 mb-4 text-center">
						<h1 class="h2 font-weight-bold mb-2">
							<?= $appName ?> Vendor Terms of Service & Agreement
						</h1>
						<h6 class="text-muted">Last updated <?= date('n/d/Y', strtotime($versionDate)); ?></h6>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<p>
							Hi. We're still working on this.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
