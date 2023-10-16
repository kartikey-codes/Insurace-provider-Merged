<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
?>
Hi <?= h($recipient_name) ?>,

A <?= h($appeal_level_name) ?> appeal on <?= $appName ?> for patient <?= h($patient_name) ?> has been marked returned by a <?= $appName ?> Professional.

This means additional documentation may be required in order to complete your appeal.
Please check the notes for any details and resubmit to <?= $appName; ?> to progress this appeal.
