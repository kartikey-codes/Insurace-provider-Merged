<?php

use Cake\Core\Configure;

$this->assign('title', __('Forgot Password'));

$appName = Configure::readOrFail('App.name');

$this->assign('preheader', __('You have requested your {0} password to be reset.', $appName));

$token = $user->password_reset_token;

$resetUrl = $this->Url->build([
	'controller' => 'Auth',
	'action' => 'resetPassword',
	'prefix' => null,
	$token,
], [
	'fullBase' => true,
]);
?>

<!-- for adding the revkeep logo in the email template -->
<div align:"center" style="width: 100%; background-color:white; " >
<table cellpadding="0" cellspacing="0" border="0" >
  <tr>
    <td style="text-align: center;">
      <img style=" width:35%; height:60px; padding-bottom:5vh; " src="https://i.ibb.co/6JC7mL7/revkeep-logo.png" alt="RevKeep">
    </td>
  </tr>
</table>

<div style="background-color: white; width:100%; padding:20px 0px; "  >
<!-- <div style="background-color: black; width:50%; position:relative;left:35%; " > -->
<h2 style="font-family: 'Poppins', sans-serif; color:rgba(53, 157, 158, 1); text-align:center; padding-top:1vh; " >Reset Your Password</h2>
<!-- </div> -->
<h3 style="text-align:center;font-family: 'Poppins', sans-serif;" >Hi <strong><?= $user->first_name ?></strong>,</h3>
<p style="text-align:center;font-family: 'Poppins', sans-serif; " class="mb-2">A password reset has been requested for your account on <?= $appName ?>.</p>
<p style="text-align:center;font-family: 'Poppins', sans-serif;" class="mb-2">
	If you did not request your password to be reset, you can ignore this message. Your password will remain unchanged.
</p>



	<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary mb-2" >
		<tbody>
			<tr>
				<td align="center">
					<table role="presentation" border="0" cellpadding="0" cellspacing="0">
						<tbody>
							<tr >
								<td>
									<a href="<?= $resetUrl; ?>" target="_blank" title="Reset your <?= $appName; ?> password" style="background-color: #359D9E; border:0px; font-family: 'Poppins', sans-serif; " >
										Reset Password
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
<p align="center" style="font-family: 'Poppins', sans-serif;" >
	You can also paste the following URL into your web browser to complete the password reset process:
</p>
<p align="center" >
	<span class="apple-link">
		<?= $resetUrl; ?>
	</span>
</p>
</div>

</div>
</div>
<!-- footer -->
<table cellpadding="" cellspacing="2px" border="0" style="padding-top: 20px; " >
  <tr >
    <td style="text-align: center;" >
      <p style="font-family: 'Poppins', sans-serif; color:#999999; font-size:12px;" >RevKeep Software, 1221 Bowers St., #2624, Birmingham, MI , 48012</p>
    </td>
  </tr>
  <tr >
    <td style="text-align: center;" >
      <p style="font-family: 'Poppins', sans-serif; color:#999999; font-size:12px;" >phone number :- 2482152975 | fax number:- 8777864977</p>
    </td>
  </tr>
  <tr>
    <td style="text-align: center;">
      <p style="font-family: 'Poppins', sans-serif; color:#999999; font-size:12px; " >email :- info@revkeepsoftware.com | website:- https://revkeepsoftware.com</p>
    </td>
  </tr>
</table>


