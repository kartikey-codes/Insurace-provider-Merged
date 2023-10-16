<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

?>
Hi <?= $user->first_name ?>,

A new vendor, <?= h($vendor->name) ?>, has just registered for <?= $appName ?>.
