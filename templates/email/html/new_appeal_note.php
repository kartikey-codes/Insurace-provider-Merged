<?php

use Cake\Core\Configure;

$this->assign('title', __('New Appeal Note'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('A new appeal note has been added on {0}.', $appName));

if ($notify == 'client') {
	$linkPath = '/client/cases/' . $case_id;
} else if ($notify == 'vendor') {
	$linkPath = '/vendors/cases/' . $case_id;
} else {
	$linkPath = ['_name' => 'login'];
}

$linkUrl = $this->Url->build($linkPath, [
	'fullBase' => true
]);
?>
<h1>New Appeal Note</h1>
<p>Hi <?= h($recipient_name) ?>,</p>
<p>A note has just been added on <?= $appName ?> for the <?= h($appeal_level_name) ?> appeal for patient <?= h($patient_name); ?>.</p>
<blockquote><?= h($note_text) ?></blockquote>
<cite class="mb-2">&mdash; <?= h($created_by_user_name) ?></cite>
<p></p>
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
