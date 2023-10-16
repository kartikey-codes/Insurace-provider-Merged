<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
?>
Hi <?= h($recipient_name) ?>,

A <?= h($appeal_level_name) ?> appeal on <?= $appName ?> for patient <?= h($patient_name) ?> has been marked completed by a <?= $appName ?> Professional.
