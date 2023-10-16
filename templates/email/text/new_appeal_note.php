<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
?>
Hi <?= h($recipient_name) ?>,

A note has just been added on <?= $appName ?> for the <?= h($appeal_level_name) ?> appeal for patient <?= h($patient_name) ?>.

<?= h($created_by_user_name) ?> wrote:
-------------------------------
<?= h($note_text) . PHP_EOL ?>
-------------------------------
