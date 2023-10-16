<?php
$this->assign('title', __('Forgot Password'));

echo $this->Form->create();
?>
<div class="container">
	<div class="row mb-4">
		<div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
			<div class="bg-white card p-4 shadow-sm rounded-lg">
				<div class="row">
					<div class="col-sm-12">
						<h1 class="h2 text-center font-weight-bold mb-2">
							Forgot Password
						</h1>
						<p class="text-center text-muted">
							Enter your email address to be sent a password reset link.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<?= $this->Flash->render() ?>
						<?= $this->Flash->render('auth') ?>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mt-4 mb-4">
						<?php
						echo $this->Form->control(
							'email',
							[
								'label' => false,
								'placeholder' => __('Email Address'),
								'class' => 'form-control input-lg py-4 mb-4',
								'required' => true,
								'id' => 'emailInput',
								'autofocus' => true
							]
						);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mb-5">
						<?php
						echo $this->Form->button(
							__('Request Password Reset'),
							[
								'class' => 'btn py-3 btn-primary btn-block btn-loader'
							]
						);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<?php
						echo $this->Html->link(
							__('Return to Login'),
							[
								'_name' => 'login'
							],
							[
								'escapeTitle' => false,
								'class' => 'font-weight-bold btn py-2 btn-link btn-block'
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

$this->start('script');
?>
<script type="text/javascript">
	document.getElementById("emailInput").focus();
</script>
<?php
$this->end();
?>
