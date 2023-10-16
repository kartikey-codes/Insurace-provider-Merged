<?php

use Cake\Core\Configure;

$this->assign('title', __('Appeal Returned'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('An appeal has been returned on {0}.', $appName));

$linkPath = '/client/cases/' . $case_id;

$linkUrl = $this->Url->build($linkPath, [
	'fullBase' => true
]);
?>
<h1>Appeal Returned</h1>
<p>Hi <?= h($recipient_name) ?>,</p>
<p>
	A <?= h($appeal_level_name) ?> appeal on <?= $appName ?> has been marked returned by a <?= $appName; ?> Professional.
</p>
<p>
	This means additional documentation may be required in order to complete your appeal.
	Please check the notes for any details and resubmit to <?= $appName; ?> to progress this appeal.
</p>
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
