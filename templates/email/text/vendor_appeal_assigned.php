<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

?>
Hi <?= h($recipient_name) ?>,

A new appeal on <?= $appName ?> for the <?= h($appeal_level_name) ?> appeal for patient <?= h($patient_name) ?> has been assigned to your vendor group.
