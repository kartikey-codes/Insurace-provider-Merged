<div class="row">
	<div class="col-12">
		<?php
		echo $this->Form->control(
			'card_name',
			[
				'label' => ['text' => __('Cardholder Name')],
				'class' => 'form-control',
				'required' => true
			]
		);
		?>
	</div>
	<div class="col-12">
		<?php
		echo $this->Form->control(
			'card_number',
			[
				'label' => ['text' => __('Credit Card Number')],
				'class' => 'form-control creditCardNumber',
				'required' => true
			]
		);
		?>
	</div>
	<div class="col-6">
		<?php
		echo $this->Form->control(
			'card_exp_month',
			[
				'label' => ['text' => __('Expiration Month')],
				'class' => 'form-control',
				'options' => array_combine(range(1, 12), range(1, 12)),
				'required' => true
			]
		);
		?>
	</div>
	<div class="col-6">
		<?php
		echo $this->Form->control(
			'card_exp_year',
			[
				'label' => ['text' => __('Expiration Year')],
				'class' => 'form-control',
				'options' => array_combine(range(intval(date('Y')), intval(date('Y')) + 10), range(intval(date('Y')), intval(date('Y')) + 10)),
				'required' => true
			]
		);
		?>
	</div>
	<div class="col-6">
		<?php
		echo $this->Form->control(
			'card_cvv',
			[
				'type' => 'text',
				'label' => ['text' => __('CVV')],
				'class' => 'form-control creditCardCvv',
				'required' => true
			]
		);
		?>
	</div>
	<div class="col-6">
		<?php
		echo $this->Form->control(
			'card_zip',
			[
				'type' => 'text',
				'label' => ['text' => __('Zip Code')],
				'class' => 'form-control zipCode',
				'required' => true
			]
		);
		?>
	</div>
</div>
