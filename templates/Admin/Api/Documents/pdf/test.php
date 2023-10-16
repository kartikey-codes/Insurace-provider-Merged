<?php

/**
 * Test Document
 */
?>
<main class="content">
	<table class="w-100 mb-4">
		<tbody>
			<tr>
				<td width="100%" class="py-2">
					<h1 class="h1">Test Document</h1>
					<?php
					if (!empty($message)) {
					?>
						<p>
							<?= $message; ?>
						</p>
					<?php
					} else {
					?>
						<p>
							No message was provided from the controller.
						</p>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="100%" class="py-2">
					<?php
					if (!empty($phpVersion)) {
					?>
						<div class="alert alert-secondary" role="alert">
							PHP version is <?= $phpVersion; ?>
						</div>
					<?php
					} else {
					?>
						<div class="alert alert-danger" role="alert">
							PHP version was not returned.
						</div>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="100%" class="py-2">
					<?php
					if (!empty($time)) {
					?>
						<div class="alert alert-info" role="alert">
							Time is <?= $time; ?>
						</div>
					<?php
					} else {
					?>
						<div class="alert alert-danger" role="alert">
							Time was not returned.
						</div>
					<?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</main>
