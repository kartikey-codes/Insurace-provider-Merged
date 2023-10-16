<?php

use Cake\Core\Configure;

$this->assign('title', __('New Appeal Assigned'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('An appeal has been assigned to you on {0}.', $appName));

$linkPath = '/vendor/cases/' . $case_id;

$linkUrl = $this->Url->build($linkPath, [
	'fullBase' => true
]);
?>
<h1>New Appeal Assigned</h1>
<p>Hi <?= h($recipient_name) ?>,</p>
<p>A new <?= h($appeal_level_name) ?> appeal on <?= $appName ?> has been assigned to your vendor group, <?= $vendor_name; ?>.</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
	<tbody>
		<tr>
			<td align="left">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td> <a href="<?= $linkUrl; ?>" target="_blank">View Appeal on <?= $appName ?></a> </td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
