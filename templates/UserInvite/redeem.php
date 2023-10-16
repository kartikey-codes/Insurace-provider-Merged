<?php
$this->assign('title', __('Join Organization'));

$loginRoute = [
	'_name' => 'login',
	'?' => [
		'inviteToken' => $token->token
	]
];

$registerRoute = [
	'_name' => 'userRegister',
	'?' => [
		'inviteToken' => $token->token
	]
];

echo $this->Form->create(null, [
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
		<div class="card-body">
			<h1 class="h2 text-dark font-weight-bold mb-2">
				Join <?= $token->inviter->organization ?>
			</h1>
			<p class="text-muted">
				You've been invited by <strong><?= $token->inviter->name ?></strong> to join <strong><?= $token->inviter->organization ?></strong> on <?= $_appName; ?>.
			</p>
			<?= $this->Flash->render(); ?>
			<?= $this->Flash->render('auth'); ?>
			<fieldset class="mt-5">
				<?php
				if (!empty($user)) {
				?>
					<div class="row mb-5">
						<div class="col-lg-4 pr-lg-5 mb-2">
							<h3 class="h4 text-dark">Existing Account</h3>
							<p class="text-muted">
								Join using your existing account.
							</p>
						</div>
						<div class="col-lg-8">
							<p>
								You're signed in as <strong><?= $user->email ?></strong>.
							</p>
							<p>
								Continuing will associate your user account with <strong><?= $token->inviter->organization ?></strong>.
							</p>
							<p class="small text-muted">
								At the moment, we only support being associated with one organization at a time. We recommend
								registering using a different email address if you are a member of multiple organizations.
							</p>
						</div>
					</div>
				<?php
				} else {
				?>
					<div class="row mb-5">
						<div class="col-lg-4 pr-lg-5 mb-2">
							<h3 class="h4 text-dark">Register</h3>
							<p class="text-muted">
								Create an account for <?= $_appName ?> in order to join your organization.
							</p>
						</div>
						<div class="col-lg-8">
							<p>
								<strong>You're not currently signed into an account for <?= $_appName; ?>.</strong>
							</p>
							<p>
								If you already have an account, you can <?= $this->Html->link(__('log in to join'), $loginRoute); ?> your organization.
							</p>
						</div>
					</div>
				<?php
				}
				?>
			</fieldset>
		</div>

		<div class="card-footer">

			<?php
			if (!empty($user)) {
			?>
				<div class="row">
					<div class="col-12 col-md-6 col-lg-4 order-2 order-md-1">
						<?php
						echo $this->Html->link(
							__('Return to Login'),
							$loginRoute,
							[
								'class' => 'btn btn-light btn-block py-2 py-lg-2 ',
								'confirm' => __('Are you sure you want to cancel registration?')
							]
						);
						?>
					</div>
					<div class="col-12 col-md-6 offset-lg-4 col-lg-4 order-1 order-md-2 mb-5 mb-md-0">
						<?php
						echo $this->Form->button(
							__('Join'),
							[
								'class' => 'btn btn-primary btn-block py-3 py-lg-2 btn-loader'
							]
						);
						?>
					</div>
				</div>
			<?php
			} else {
			?>
				<div class="row">
					<div class="col-12 col-md-6 col-lg-4 order-2 order-md-1">
						<?php
						echo $this->Html->link(
							__('Log In'),
							$loginRoute,
							[
								'class' => 'btn btn-light btn-block py-2 py-lg-2 ',
								'confirm' => __('Are you sure you want to cancel registration?')
							]
						);
						?>
					</div>
					<div class="col-12 col-md-6 offset-lg-4 col-lg-4 order-1 order-md-2 mb-5 mb-md-0">
						<?php
						echo $this->Html->link(
							__('Create Account'),
							$registerRoute,
							[
								'class' => 'btn btn-primary btn-block py-2 py-lg-2 ',
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
<?php
echo $this->Form->end();
?>
