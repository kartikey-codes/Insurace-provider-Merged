<?php
$this->assign('title', __('Reset Password'));

echo $this->Form->create($user);
?>
<div class="container">
	<div class="row mb-4">
		<div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
			<div class="bg-white card p-4 shadow-sm rounded-lg">
				<div class="row">
					<div class="col-sm-12 mb-4">
						<h1 class="h2 text-center font-weight-bold mb-2">
							Enter New Password
						</h1>
						<p class="text-center text-muted">
							Please create a new password for your account.
						</p>
						<?= $this->Flash->render() ?>
						<?= $this->Flash->render('auth') ?>
					</div>
				</div>

				<?php
				echo $this->Form->hidden(
					'Users.id'
				);
				?>
				<div class="row">
					<div class="col-12 mb-4">
						<?php
						echo $this->Form->control(
							'password',
							[
								'label' => __('New Password'),
								'class' => 'form-control input-lg py-4',
								'required' => true,
								'type' => 'password',
								'value' => ''
							]
						);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mb-4">
						<?php
						echo $this->Form->control(
							'confirm_password',
							[
								'label' => __('Confirm Password'),
								'class' => 'form-control input-lg py-4',
								'required' => true,
								'type' => 'password',
								'value' => '',
							]
						);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mt-4">
						<?php
						echo $this->Form->button(
							__('Change Password'),
							[
								'class' => 'btn btn-primary btn-lg btn-block py-3 btn-loader'
							]
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->Form->end();
?>
