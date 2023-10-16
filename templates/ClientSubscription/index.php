<?php

use Cake\Core\Configure;

$this->assign('title', __('Subscription Plan'));

$debugging = Configure::read('debug');
$maxLicenses = Configure::readOrFail('Subscriptions.maxLicenses');

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
	<div class="bg-white border card p-4 shadow rounded-lg">
		<div class="row mb-5">
			<div class="col-sm-12">
				<h1 class="h2 text-dark font-weight-bold mb-2">
					Subscription Set Up
				</h1>
				<p class="text-muted">
					A subscription is required in order to use <?= $_appName; ?>.
					Pricing is calculated after selecting licenses and plan.
				</p>
				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Licenses</h3>
				<p class="text-muted">
					<?= $_appName; ?> is billed per professional (based on NPI number) associated with your audits.
				</p>
			</div>
			<div class="col-lg-8">
				<?php
				echo $this->Form->control(
					'licenses',
					[
						'type' => 'number',
						'default' => 1,
						'min' => 1,
						'max' => $maxLicenses,
						'step' => 1,
						'label' => ['text' => __('Number of Professionals')],
						'class' => 'form-control form-control-lg'
					]
				);
				?>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h4 text-dark">Plan</h3>
				<p class="text-muted">
					Subscriptions are billed automatically and you can cancel at any time.
				</p>
			</div>
			<div class="col-lg-8">
				<label for="product_id">Plan</label>
				<?php
				if (!empty($products)) {
					foreach ($products as $i => $product) {
				?>
						<div class="card shadow-sm p-4">
							<div class="row">
								<div class="col-1">
									<?php
									echo $this->Form->radio('product_id', [$product->id => $product->name], [
										'empty' => false,
										'label' => false,
										'value' => $form->getData('product_id'),
										'default' => $i == 0 ? $product->id : '',
										'class' => 'mb-2'
									]);
									?>
								</div>
								<div class="col-11">
									<h5 class="mb-1"><?= $product->name; ?></h5>
									<p class=" text-muted mb-0">
										<?= $product->description; ?>
									</p>

									<?php
									if ($product->recurringPrice > 0) {
									?>
										<h6 class="h2">
											<?= $this->Number->currency($product->recurringPrice); ?>
											<small class="text-muted"><?= $product->recurringInterval; ?>ly</small>
										</h6>
										<h6 class="h5 text-muted">
											per professional
										</h6>
									<?php
									}
									?>
								</div>
							</div>
						</div>
					<?php
					}
				} else {
					?>
					<div class="alert alert-light p-5" role="alert">
						<h3>Oops!</h3>
						<p class="font-weight-bold">
							It looks like we don't have any plans to display.
						</p>
						<p class="font-italic">
							This can happen if there was an error attempting to contact our payment provider.
							Usually this is something that should resolve itself very shortly after a page refresh.
							If you continue to have problems, please contact support.
						</p>
					</div>
					<?php
					if ($debugging) {
					?>
						<div class="alert alert-danger shadow-sm" role="alert">
							<strong>Development Notice:</strong> Check with the subscription provider, such as Stripe, to make sure
							products are configured in the dashboard.
						</div>
					<?php
					}
					?>
				<?php
				}
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-12 offset-md-6 col-md-6 offset-lg-8 col-lg-4">
				<?php
				if (!empty($products)) {
					echo $this->Form->button(
						__('Submit'),
						[
							'id' => 'submitButton',
							'class' => 'btn py-3 btn-primary btn-block btn-loader'
						]
					);
				} else {
					echo $this->Html->link(
						'Refresh',
						$this->getRequest()->getRequestTarget(),
						[
							'class' => 'btn py-3 btn-primary btn-block btn-loader'
						]
					);
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->Form->end();
?>
