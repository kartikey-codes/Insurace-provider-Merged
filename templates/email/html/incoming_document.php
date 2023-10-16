<?php

use Cake\Core\Configure;

$this->assign('title', __('Incoming Document'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('A new incoming document has just been uploaded to {0}.', $appName));

$linkUrl = $this->Url->build([
	'_name' => 'login'
], [
	'fullBase' => true
]);
?>
<h1>Incoming Document</h1>
<p>Hi <?= $user->first_name ?>,</p>
<p>A new incoming document has just been uploaded to <?= $appName ?>.</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
	<tbody>
		<tr>
			<td align="left">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td> <a href="<?= $linkUrl; ?>" target="_blank">Log in to <?= $appName ?></a> </td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
