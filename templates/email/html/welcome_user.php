<?php

use Cake\Core\Configure;

$this->assign('title', __('Successfully Registered'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('You have requested your {0} password to be reset.', $appName));

?>

<!-- for adding the revkeep logo in the email template -->
<div style="background-color:  rgb(236,254,254);" align:"center" >
<table cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td style="text-align: center;">
      <img style=" left:35%; width:20%; height:60px;" src="https://i.ibb.co/6JC7mL7/revkeep-logo.png" alt="RevKeep">
    </td>
  </tr>
<br>

<br>

<div style="background-color: white; width:90%; margin-left:5%;"  >
<!-- <div style="background-color: black; width:50%; position:relative;left:35%; " > -->
<br>
<h2 style="font-family: 'Poppins', sans-serif; color:rgba(53, 157, 158, 1); text-align:center;" >Welcome to RevKeep</h2>
<!-- </div> -->
<h3 style="text-align:center;" >Hi <strong><?= $fullname ?></strong>,</h3>

<p align='center' class="mb-2">Your account has been created on <?= $appName ?>.</p>
<p align='center' class="mb-2">Your account's password is <strong><?= $password ?></strong> .</p>

<p align='center' class="mb-4">
	Thanks again for joining <?= $appName; ?>!
</p>
<p align='center' class="text-muted">
	If you did not request an account, your organization may have registered an account for you. Please contact support if you believe this is an error.
</p>

</div>
<hr />


</div>
</div>
