<?php
$this->assign('title', __('Subscription Payment'));

$this->start('script');
?>
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
	const stripePk = '<?= $stripePk; ?>';
	const clientSecret = '<?= $clientSecret; ?>';
	const stripe = Stripe(stripePk);
	const elements = stripe.elements();
	const card = elements.create('card');
	card.mount('#card-element');
	card.on('change', function(event) {
		displayError(event);
	});

	const btn = document.querySelector('#subscribeButton');

	function displayError(event) {
		//changeLoadingStatePrices(false);

		let errorInput = document.getElementById('card-element');
		let displayError = document.getElementById('card-element-errors');
		if (event.error) {
			displayError.textContent = event.error.message;
			$(errorInput).addClass('is-invalid');
		} else {
			displayError.textContent = '';
			$(errorInput).removeClass('is-invalid');
		}
	}

	function post(path, params, method = 'post') {
		const form = document.createElement('form');
		form.method = method;
		form.action = path;

		for (const key in params) {
			if (params.hasOwnProperty(key)) {
				const hiddenField = document.createElement('input');
				hiddenField.type = 'hidden';
				hiddenField.name = key;
				hiddenField.value = params[key];

				form.appendChild(hiddenField);
			}
		}

		document.body.appendChild(form);
		form.submit();
	}

	function paymentSucceeded(result) {
		$('#subscribeButton')
			.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`)
			.prop("disabled", true);

		console.log("Payment succeeded.", result);
		post('/subscribe/client/payment/post', result.paymentIntent);
	}

	btn.addEventListener('click', async (e) => {
		e.preventDefault();

		const nameInput = document.getElementById('cardholderName');

		if (!nameInput.value || nameInput.value == "") {
			alert("Please enter a cardholder name.");
			nameInput.focus();
			return;
		}

		$(btn)
			.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`)
			.prop("disabled", true);

		// Create payment method and confirm payment intent.
		stripe.confirmCardPayment(clientSecret, {
			payment_method: {
				card: card,
				billing_details: {
					name: nameInput.value,
				},
			}
		}).then((result) => {
			$(btn)
				.text('Subscribe')
				.prop("disabled", false);

			if (result.error) {
				if (result.error.payment_intent.status == 'succeeded') {
					alert('Your subscription has already been confirmed and paid.');
				} else {
					alert(result.error.message);
				}
			} else {
				// Successful subscription payment
				paymentSucceeded(result);
			}
		});
	});
</script>
<?php
$this->end();

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
					Please review your plan details and complete payment to continue.
				</p>

				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12 col-lg-4">
				<?php
				echo $this->Html->link(
					'Edit Subscription',
					[
						'action' => 'index'
					],
					[
						'class' => 'btn btn-light d-block d-lg-inline-block mb-4'
					]
				);
				?>
			</div>
			<div class="col-12 col-lg-8 mb-4">
				<div class="card shadow-sm mb-5">
					<div class="p-4">
						<h4><?= $subscription->name; ?></h4>
						<p class="text-muted mb-2"><?= $subscription->description; ?></p>
						<p class="mb-0">
							<strong><?= $client->licenses; ?></strong>
							<span class="text-muted">license<?= $client->licenses !== 1 ? 's' : '' ?></span>
						</p>

						<?php
						if ($subscription->recurringPrice > 0) {
						?>
							<p class="mb-4">
								<strong><?= $this->Number->currency($subscription->recurringPrice); ?></strong>
								<span class="text-muted">per license <?= $subscription->recurringInterval; ?>ly</span>
							</p>
						<?php
						}
						?>
					</div>
					<div class="p-4">
						<h6 class="h3">
							<?= $this->Number->currency($price); ?>
							<small class="text-muted">total per <?= $subscription->recurringInterval; ?></small>
						</h6>
					</div>

					<h6 class="h6 text-muted">
						<?= $licenses ?> license(s)
					</h6>

				</div>

				<div class="form-group">
					<?php
					echo $this->Form->control(
						'cardholder_name',
						[
							'autofocus',
							'type' => 'text',
							'required' => 'required',
							'id' => 'cardholderName',
							'class' => 'form-control'
						]
					);
					?>
				</div>

				<div class="form-group mt-4">
					<label for="card-element">Credit Card</label>
					<div id="card-element" class="form-control" style='height: 2.4em; padding-top: .7em;'>
						<!-- Stripe credit card -->
					</div>
					<div id="card-element-errors" class="invalid-feedback" role="alert"></div>
				</div>

				<p class="small text-muted">
					We utilize <a href="https://stripe.com/" target="_blank">Stripe</a> to process subscriptions.
					Your credit card is never sent to our servers.
				</p>

			</div>
		</div>

		<div class="row mt-5">
			<div class="col-12 offset-lg-8 col-lg-4">
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
