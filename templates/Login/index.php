<?php

use Cake\Core\Configure;

$this->assign('title', __('Login'));

/** @var bool */
$registrationEnabled = Configure::readOrFail('Registration.enabled');

echo $this->Form->create(null);

?>
<div class="container">
	<div class="row mb-4">
		<div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
			<div class="bg-white card p-4 shadow-md rounded-lg">
				<?= $this->Flash->render() ?>
				<?= $this->Flash->render('auth') ?>
				<fieldset>
					<?php
					if (!empty($_user)) {
					?>
						<div class="p-4 bg-light rounded shadow-sm mb-5">
							<h6 class="mb-4">Signed in as...</h6>

							<div class="media">
								<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-circle align-self-start mr-3 text-primary" viewBox="0 0 16 16">
									<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
									<path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
								</svg>
								<div class="media-body">
									<div class="row">
										<div class="col-12 col-lg-8">
											<h5 class="h5 mt-0 mb-1"><?= $_user['full_name']; ?></h5>
											<p class="small text-muted"><?= $_user['email']; ?></p>
										</div>
										<div class="col-12 col-lg-4 text-left text-lg-right">
											<?php
											echo $this->Html->link(
												__('Log Out'),
												[
													'_name' => 'logout'
												],
												[
													'escapeTitle' => false,
													'class' => 'btn btn-link font-weight-bold mx-0',
													'confirm' => __('Are you sure you want to log out and close your session?')
												]
											);
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						if ($_user->hasAssociatedArea()) {
						?>
							<?php
							if ($_user->isClientUser()) {
							?>
								<div class="row">
									<div class="col-12 mb-4">
										<?php
										if (!empty($_client->name)) {
										?>
											<h6 class="text-muted mb-2"><?= $_client->name ?></h6>
										<?php
										}
										?>
										<?php
										echo $this->Html->link(
											$_user->isVendorUser() ? __('Client Dashboard') : __('Dashboard'),
											[
												'_name' => 'clientSpa'
											],
											[
												'escapeTitle' => false,
												'class' => 'btn btn-primary btn-lg btn-block mb-2 py-3'
											]
										);
										?>
									</div>
								</div>
							<?php
							}

							if ($_user->isVendorUser()) {
							?>
								<div class="row">
									<div class="col-12 mb-4">
										<?php
										if (!empty($_vendor->name)) {
										?>
											<h6 class="text-muted mb-2"><?= $_vendor->name ?></h6>
										<?php
										}
										?>
										<?php
										echo $this->Html->link(
											$_user->isClientUser() ? __('Vendor Dashboard') : __('Dashboard'),
											[
												'_name' => 'vendorSpa'
											],
											[
												'escapeTitle' => false,
												'class' => 'btn btn-primary btn-lg btn-block mb-2 py-3'
											]
										);
										?>
									</div>
								</div>
							<?php
							}

							if ($_user->isAdmin()) {
							?>
								<div class="row">
									<div class="col-12 mb-4">
										<?php
										echo $this->Html->link(
											__('Admin Dashboard'),
											[
												'_name' => 'adminSpa'
											],
											[
												'escapeTitle' => false,
												'class' => 'btn btn-primary btn-lg btn-block mb-2 py-3'
											]
										);
										?>
									</div>
								</div>
							<?php
							}
							?>
						<?php
						} else {
						?>
							<div>
								<h5>I'm part of a healthcare organization.</h5>
								<?php
								echo $this->Html->link(
									__('Create Organization'),
									[
										'_name' => 'clientRegister'
									],
									[
										'escapeTitle' => false,
										'class' => 'btn btn-primary btn-lg btn-block mb-4 py-3'
									]
								);
								?>
							</div>
							<div class="mt-5">
								<h5>I'm an audit professional.</h5>
								<?php
								echo $this->Html->link(
									__('Vendor Registration'),
									[
										'_name' => 'vendorRegister'
									],
									[
										'escapeTitle' => false,
										'class' => 'btn btn-light btn-block mb-4 py-3'
									]
								);
								?>
							</div>
						<?php
						}
						?>
					<?php
					} else {
					?>
						<div class="mt-2">
							<?php
							echo $this->Form->control(
								'email',
								[
									'type' => 'email',
									'label' => __('Email Address'),
									'class' => 'form-control input-lg py-4 mb-4',
									'required' => true,
									'autofocus' => true
								]
							);

							echo $this->Form->control(
								'password',
								[
									'type' => 'password',
									'label' => __('Password'),
									'class' => 'form-control input-lg py-4',
									'required' => true,
									'default' => null
								]
							);
							?>
						</div>
						<div class="row my-3">
							<div class="col text-right">
								<?php
								echo $this->Html->link(
									__('Forgot Password'),
									[
										'_name' => 'forgotPassword'
									],
									[
										'class' => 'font-weight-bold text-decoration-none',
										'tabindex' => '-1'
									]
								);
								?>
							</div>
						</div>
					<?php
					}
					?>
				</fieldset>

				<?php
				if (empty($_user)) {
				?>
					<div class="row">
						<div class="col mt-2">
							<?php
							echo $this->Form->button(
								__('Log In'),
								[
									'type' => 'submit',
									'class' => 'btn py-3 btn-primary btn-block btn-loader'
								]
							);
							?>
						</div>
					</div>
				<?php
				}
				?>

				<?php
				if ($registrationEnabled && empty($_user)) {
				?>
					<div class="row mt-5">
						<div class="col col-md-12">
							<?php
							echo $this->Html->link(
								__('Create New Account'),
								[
									'_name' => 'userRegister'
								],
								[
									'class' => 'btn py-3 btn-light btn-block'
								]
							);
							?>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->Form->end();
?>
