<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

?>
Hi <?= $user->first_name ?>,

A new client, <?= h($client->name) ?>, has just registered for <?= $appName ?>.
