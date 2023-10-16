<?php

use Cake\Core\Configure;

$this->assign('title', __('New Vendor Registration'));

$appName = Configure::readOrFail('App.name');

echo $this->Form->create($form, [
	'type' => 'post',
	'valueSources' => [
		'context',
		'data'
	],
]);
?>
<div class="container mb-5">
	<div class="bg-white border card p-4 shadow-sm rounded-lg">
		<div class="row">
			<div class="col-sm-12 mb-5">
				<h1 class="h2 text-dark font-weight-bold mb-2">
					Vendor Registration
				</h1>
				<p class="text-muted">
					Register as a <?= $appName ?> Professional to access your vendor dashboard and see assigned cases.
				</p>
				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Your Organization</h3>
				<p class="text-muted">
					Basic information about your organization as a professional group. If you're a single person operation, just use your full name instead.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-md-12">
						<?php
						echo $this->Form->control(
							'name',
							[
								'type'  => 'text',
								'label' => ['text' => __('Company Name')],
								'class' => 'form-control input-lg'
							]
						);
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Contact</h3>
				<p class="text-muted">
					Any contact information related to your organization.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'phone',
							[
								'type'  => 'text',
								'label' => ['text' => __('Phone Number')],
								'class' => 'form-control phoneNumber',
							]
						);
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Address</h3>
				<p class="text-muted">
					Your organization's primary mailing/billing address.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-md-12 col-lg-6">
						<?php
						echo $this->Form->control(
							'street_address_1',
							[
								'type'  => 'text',
								'label' => ['text' => __('Address')],
								'class' => 'form-control',
							]
						);
						?>
					</div>
					<div class="col-md-12 col-lg-6">
						<?php
						echo $this->Form->control(
							'street_address_2',
							[
								'type'  => 'text',
								'label' => ['text' => __('Address Continued')],
								'class' => 'form-control mb-1',
								'placeholder' => 'Suite, floor, etc...'
							]
						);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'city',
							[
								'type'  => 'text',
								'label' => ['text' => __('City')],
								'class' => 'form-control',
							]
						);
						?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'state',
							[
								'options' => $states,
								'type'  => 'select',
								'label' => ['text' => __('State')],
								'class' => 'form-control',
								'disabled' => ['']
							]
						);
						?>
					</div>

					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'zip',
							[
								'type'  => 'text',
								'label' => ['text' => __('Zip Code')],
								'class' => 'form-control zipCode',
							]
						);
						?>
					</div>

				</div>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Terms & Conditions</h3>
				<p class="text-muted">
					You must agree to the terms and conditions of use in order to register.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-md-12">

						<ul class="list-unstyled">
							<li>
								<?php
								echo $this->Html->link('Terms & Conditions', ['_name' => 'vendorTerms'], ['class' => 'text-link font-weight-bold', 'target' => '_blank']);
								?>
							</li>
						</ul>

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
			<div class="col-md-6 col-lg-4">
				<?php
				echo $this->Html->link(
					__('Cancel'),
					[
						'_name' => 'login'
					],
					[
						'class' => 'btn btn-light py-3 btn-block mb-4 mb-md-0',
						'confirm' => __('Are you sure you want to cancel registration?')
					]
				);
				?>
			</div>
			<div class="col-md-6 offset-lg-4 col-lg-4">
				<?php
				echo $this->Form->button(
					__('Register'),
					[
						'class' => 'btn btn-primary py-3 btn-block btn-loader'
					]
				);
				?>
			</div>
		</div>

	</div>
</div>
<?php
echo $this->Form->end();
?>
