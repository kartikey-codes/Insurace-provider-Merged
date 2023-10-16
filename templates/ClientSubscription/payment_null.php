<?php
$this->assign('title', __('Subscription Payment'));

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
					Payment
				</h1>
				<p class="text-muted">
					A paid subscription is required in order to use <?= $appName; ?>. You can cancel at any time.
				</p>
				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h3 text-dark">Subscription</h3>
				<p class="text-muted">
					Review your subscription plan.
				</p>
			</div>
			<div class="col-lg-8 mb-4">

				<div class="card p-4 shadow-sm">
					<h5><?= $subscription->name; ?></h5>
					<p class="text-muted">
						<?= $subscription->description; ?>
					</p>

					<h6 class="h2">
						<?= $this->Number->currency($subscription->recurringPrice); ?>
						<small class="text-muted">/<?= $subscription->recurringInterval; ?></small>
					</h6>

					<h6 class="h6 text-muted">
						<?= $licenses ?> license(s)
					</h6>
				</div>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h3 text-dark">Payment</h3>
				<p class="text-muted">
					You're working in development mode on the app, so there is no payment information to collect.
				</p>
			</div>
			<div class="col-lg-8 mb-4">
				<div class="alert alert-warning" role="alert">
					No payment information is required when in development mode.
				</div>
			</div>
		</div>

		<div class="row">
			<div class="offset-md-6 col-md-6 offset-lg-8 col-lg-4">
				<?php
				echo $this->Form->button(
					__('Subscribe'),
					[
						'id' => 'subscribeButton',
						'class' => 'btn py-3 btn-primary btn-block btn-loader'
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
