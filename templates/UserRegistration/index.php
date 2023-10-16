<?php

use Cake\Core\Configure;

$this->assign('title', __('User Registration'));

$appName = Configure::readOrFail('App.name');

$termsUrl = $this->Url->build([
	'_name' => 'clientTerms',
], [
	'fullBase' => true,
]);

echo $this->Form->create($form, [
	'id' => 'form',
	'type' => 'post',
	'valueSources' => [
		'context',
		'data'
	],
]);
?>
<div class="container mb-5">
	<div class="bg-white card p-4 shadow-sm rounded-lg">
		<div class="row">
			<div class="col-sm-12 mb-4">
				<h1 class="h2 text-dark font-weight-bold mb-2">
					New User Registration
				</h1>
				<p class="text-muted">
					A user account is required to access <?= $appName; ?>. After creating your user account, you have the option to create a new Organization or join an existing one.
				</p>
				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
				<?php
				echo $this->Form->hidden(
					'invite_token',
					[
						'value' => $inviteToken
					]
				);
				?>
			</div>
		</div>
		<fieldset>
			<div class="row mb-5">
				<div class="col-lg-4 pr-lg-5 mb-2">
					<h3 class="h4 text-dark">About You</h3>
					<p class="text-muted">
						This will be used as your display name and contact information.
					</p>
				</div>
				<div class="col-lg-8">
					<div class="row mb-lg-4">
						<div class="col-12 col-md-6">
							<?php
							echo $this->Form->control(
								'first_name',
								[
									'type' => 'text',
									'label' => __('First Name'),
									'class' => 'form-control py-3',
									'required' => true,
									'default' => null,
									'autofocus' => true
								]
							);
							?>
						</div>
						<div class="col-12 col-md-6">
							<?php
							echo $this->Form->control(
								'middle_name',
								[
									'type' => 'text',
									'label' => __('Middle Name'),
									'class' => 'form-control py-3',
									'required' => true,
									'default' => null,
									'autofocus' => true
								]
							);
							?>
						</div>

						<div class="col-12 col-md-6">
							<?php
							echo $this->Form->control(
								'last_name',
								[
									'type' => 'text',
									'label' => __('Last Name'),
									'class' => 'form-control py-3',
									'required' => true,
									'default' => null
								]
							);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<?php
							echo $this->Form->control(
								'email',
								[
									'type' => 'email',
									'label' => __('Email Address'),
									'class' => 'form-control py-3',
									'required' => true
								]
							);
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row mb-5">
				<div class="col-lg-4 pr-lg-5 mb-2">
					<h3 class="h4 text-dark">Password</h3>
					<p class="text-muted">
						Passwords must be at least <?= $minPasswordLength ?: 6; ?> characters.
					</p>
				</div>
				<div class="col-lg-8">
					<div class="row">
						<div class="col-md-6">
							<?php
							echo $this->Form->control(
								'password',
								[
									'type' => 'password',
									'label' => __('Password'),
									'class' => 'form-control py-3',
									'required' => true,
									'default' => null
								]
							);
							?>
						</div>
						<div class="col-md-6">
							<?php
							echo $this->Form->control(
								'confirm_password',
								[
									'type' => 'password',
									'label' => __('Confirm Password'),
									'class' => 'form-control py-3',
									'required' => true,
									'default' => null
								]
							);
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row mb-5">
				<div class="col-lg-4 pr-lg-5 mb-2">
					<h3 class="h4 text-dark">Terms of Service</h3>
					<p class="text-muted">
						You must agree to the terms and conditions of <?= $appName; ?> in order to register an account.
					</p>
				</div>
				<div class="col-lg-8">
					<div class="row">
						<div class="col-12 mb-3">
							<?php
							echo $this->Html->link(
								__('View Terms & Conditions'),
								$termsUrl,
								[
									'class' => 'btn btn-lg btn-link font-weight-bold', 'target' => '_blank',
								]
							);
							?>
						</div>
						<div class="col-12">
							<?php
							echo $this->Form->control(
								'accept_terms',
								[
									'label' => __('I accept the terms and conditions.')
								]
							);
							?>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 col-lg-4 order-2 order-md-1">
					<?php
					echo $this->Html->link(
						__('Return to Login'),
						[
							'_name' => 'login'
						],
						[
							'class' => 'btn btn-light py-3 btn-block',
							'confirm' => __('Are you sure you want to cancel registration?')
						]
					);
					?>
				</div>
				<div class="col-md-6 offset-lg-4 col-lg-4 order-1 order-md-2 mb-5 mb-md-0">
					<?php
					echo $this->Form->button(
						__('Create Account'),
						[
							'class' => 'btn btn-primary py-3 btn-block btn-loader'
						]
					);
					?>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<?php
echo $this->Form->end();
?>
