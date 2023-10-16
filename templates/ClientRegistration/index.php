<?php

use Cake\Core\Configure;

$this->assign('title', __('New Client Registration'));

$appName = Configure::readOrFail('App.name');

$this->start('script');
?>
<script>
	const lookupUrl = '<?= $npiLookupUrl; ?>';
	const clientName = $('#clientName');
	const clientState = $('#clientState');
	const clientNameError = $('#clientNameError');
	const searchButton = $('#searchOrganizations');
	const resultList = $('#npiResults');
	var searching = false;

	const npiLookup = (value, state = '') => fetch(`${lookupUrl}?name=${value}&state=${state}`, {
			method: 'GET'
		})
		.then(response => response.json().then(data => {
			searching = false;

			if (!response.ok) {
				throw Error(data.error || 'HTTP error');
			} else {
				return data.results || [];
			}
		}));

	const selectNpiButton = (result) => {
		var btn = document.createElement('button');
		var txt = document.createTextNode("Choose");
		btn.setAttribute('class', 'btn btn-primary');
		btn.appendChild(txt);
		btn.setAttribute('type', 'button');
		btn.setAttribute('id', 'btn-' + result.number);
		btn.setAttribute('data-result', JSON.stringify(result));
		return btn;
	};

	const populateResults = (results) => results.forEach(result => {
		const locationAddress = result.addresses.find(address => {
			return address.address_purpose == "LOCATION" ? address : false;
		});

		const otherNames = result.other_names || [];
		const otherNamesList = otherNames.map(name => name.organization_name);
		const taxonomiesList = result.taxonomies.map(taxonomy => taxonomy.desc);

		const selectButton = selectNpiButton(result);
		$(selectButton).on('click', (event) => {
			const result = $(selectButton).data('result');
			chooseNpiResult(result);
		});

		const resultItem = $(`
			<li class="list-group-item">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="small text-muted mb-0">#${result.number}</p>
						<h5 class="font-weight-bold mb-1 mt-1">${result.organization_name}</h5>
						${otherNamesList.length > 0 ? '<p class="font-weight-normal text-muted mb-0">DBA: ' + otherNamesList.join(", ") + '</p>' : ''}
						<p class="small font-weight-normal text-muted mb-0">${locationAddress.address_1}${locationAddress.address_2 ? ' ' + locationAddress.address_2 : ''}, ${locationAddress.city}, ${locationAddress.state} ${locationAddress.postal_code}</p>
					</div>
					<div>
						<div class="selectButton ml-4"></div>
					</div>
				</div>
				${taxonomiesList.length > 0 ? '<p class="font-weight-normal text-muted mt-2 mb-0">' + taxonomiesList.join(", ") + '</p>' : ''}
			</li>
			`);

		resultItem
			.find('.selectButton')
			.append(selectButton);

		resultItem
			.appendTo(resultList);
	});

	const chooseNpiResult = (result) => {
		$('.npiNumber').val(result.number);
		$('.companyName').val(result.name);
		resultList.empty();
	};

	const doSearch = (value) => {
		const state = clientState.val();
		clientState.attr('readonly', false);
		clientName.attr('readonly', false);
		searchButton.attr('disabled', false);

		if (!value) {
			resultList.empty();
			clientNameError.text("Enter your organization name to search...");

			return;
		} else {
			searchButton.attr('disabled', true);

			resultList.html(`
				<li class="list-group-item">
					Searching...
				</li>
			`);

			clientName.attr('readonly', true);
			clientState.attr('readonly', true);

			clientNameError.text('');
		}

		searching = true;

		npiLookup(value, state).then(data => {
				resultList.empty();

				if (data == [] || data.length == 0) {
					resultList.append(`
					<li class="list-group-item list-group-item-warning">
						No results found.
					</li>
				`);
				}

				populateResults(data);
			})
			.catch(error => {
				resultList.empty();
				clientNameError.text(error.message);
			})
			.finally(data => {
				clientState.attr('readonly', false);
				clientName.attr('readonly', false);
				searchButton.attr('disabled', false);
				searching = false;
			});
	};

	clientName.on('keydown', function(e) {
		if (e.key === 'Enter' || e.keyCode === 13) {
			e.stopPropagation();
			e.preventDefault();

			if (searching) {
				return;
			}

			var $this = $(this);
			var value = $this.val();

			doSearch(value);
		}
	});

	searchButton.on('click', function() {
		var $this = $(this);
		var value = clientName.val();

		doSearch(value);
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
		<div class="row">
			<div class="col-sm-12 mb-5">
				<h1 class="h2 text-dark font-weight-bold mb-2">
					Register Client Organization
				</h1>
				<p class="text-muted">
					Sign up your organization that receives post-payment audits to access <?= $appName ?>.
				</p>
				<?= $this->Flash->render(); ?>
				<?= $this->Flash->render('auth'); ?>
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-lg-4 pr-lg-5 mb-2">
				<h3 class="h3 text-dark">Your Organization</h3>
				<p class="text-muted">
					We utilize the <a href="https://npiregistry.cms.hhs.gov/" target="_blank">NPPES NPI Registry</a> to verify organizations and retrieve address, contact and taxonomy information.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-12">
						<label for="state">State</label>

						<?php
						echo $this->Form->control(
							'state',
							[
								'id' => 'clientState',
								'label' => false,
								'class' => 'form-control state',
								'options' => $states
							]
						);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<label for="client_name">Organization Name</label>
					</div>
				</div>
				<div class="row">
					<div class="col-8 col-lg-10 mb-4">
						<?php
						echo $this->Form->control(
							'name',
							[
								'id' => 'clientName',
								'label' => false,
								'class' => 'form-control companyName',
								'placeholder' => 'Your company\'s name in the NPI Registry.',
								'autocomplete' => 'off',
							]
						);
						?>
						<div id="clientNameError" class="text-danger mb-2"></div>
					</div>
					<div class="col-4 col-lg-2">
						<button type="button" id="searchOrganizations" class="btn btn-primary btn-block mb-4">Search</button>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div id="npiResults" class="list-group mb-4 overflow-y-scroll mh-40"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php
						echo $this->Form->control(
							'npi_number',
							[
								'label' => ['text' => __('Primary NPI Number')],
								'class' => 'form-control npiNumber',
								'readonly' => true,
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
				<h3 class="h3 text-dark">Contact</h3>
				<p class="text-muted">
					Your organization's default contact person responsible for the billing account.
				</p>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'email',
							[
								'label' => ['text' => __('Email Address')],
								'class' => 'form-control',
							]
						);
						?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->Form->control(
							'phone',
							[
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
			<div class="col-lg-4 pr-lg-5 mb-4">
				<h3 class="h3 text-dark">Terms & Conditions</h3>
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
								echo $this->Html->link('Terms & Conditions', ['_name' => 'clientTerms'], ['class' => 'text-link font-weight-bold', 'target' => '_blank']);
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
						'id' => 'registerButton',
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
