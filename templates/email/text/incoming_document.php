<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');
?>
Hi <?= $user->first_name ?>,

A new incoming document has been uploaded on <?= $appName ?>.
