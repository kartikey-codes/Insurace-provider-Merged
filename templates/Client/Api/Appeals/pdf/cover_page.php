<?php

use Cake\Core\Configure;

/**
 * Appeal Letter PDF Template
 */
$dateFormat = 'm/d/Y';

$caseType = $data->case?->case_type?->name ?: 'Case Review';
$appealLevel = $data->appeal_level?->short_name;
$appealType = $data->appeal_type?->name ?: '';
$facility = $data->case?->facility?->name ?: null;

$patientDisplayName = $data->case?->patient?->document_name ?: '(Missing Patient)';
$patientDOB = $data->case?->patient?->date_of_birth ?: null;
$patientAge = $data->case?->patient?->age ?: 0;

$documentTitle = sprintf(
	'%s - %s',
	$data->appeal_level?->short_name ?: "(Level)",
	$data->case?->patient?->document_name ?: "(Patient)"
);

$this->assign('title', $documentTitle);
?>
<main class="content">
	<!-- Logo -->
	<table class="w-100 mb-4">
		<tbody>
			<tr>
				<td width="100%" class="py-2">
					<?= $this->Html->image($this->Pdf->absoluteImagePath('RevKeep_tag_72dpi.jpg'), [
						'width' => 120
					]); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Header -->
	<table class="w-100 mb-4">
		<tbody>
			<tr>
				<td width="66.66%">
					<h1 class="h2 my-0 text-uppercase font-weight-bold">
						<?= $patientDisplayName; ?>
					</h1>
					<h3 class="small h5 my-0 text-uppercase text-muted font-weight-bold">
						<?= $appealType ?>
					</h3>
					<h3 class="small h5 my-0 text-uppercase text-muted font-weight-bold">
						<?= $appealLevel ?>
					</h3>
				</td>
				<td width="33.33%" class="text-right">
					<?php
					if (!empty($patientDOB)) {
					?>
						<h3 class="d-block my-0 small text-uppercase text-muted">
							<span>
								<span class="font-weight-bold">
									DOB
								</span>
								<?= $patientDOB->format($dateFormat) ?>
								<?php
								if ($patientAge > 0) {
								?>
									<div>
										<span class="font-weight-bold">
											Age
										</span>
										<?= number_format($patientAge); ?>
									</div>
								<?php
								}
								?>
							</span>
						</h3>
					<?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Summary -->
	<table class="table table-sm mb-4" cellpadding="0" cellspacing="0">
		<tr>
			<th class="small text-uppercase font-weight-bold text-muted">
				Facility
			</th>
			<td>
				<?php
				if (!empty($facility)) {
					echo $facility;
				} else {
				?>
					<span class="text-muted">
						&mdash;
					</span>
				<?php
				}
				?>
			</td>
			<th class="small text-uppercase font-weight-bold text-muted">
				Visit ID
			</th>
			<td>
				<?php
				if (!empty($data->case->visit_number)) {
					echo $data->case->visit_number;
				} else {
				?>
					<span class="text-muted">
						&mdash;
					</span>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th class="small text-uppercase font-weight-bold text-muted">
				Admitted
			</th>
			<td>
				<?php
				if ($data->case->admit_date) {
					echo $data->case->admit_date->format($dateFormat);
				} else {
				?>
					<div class="text-muted text-uppercase">
						&mdash;
					</div>
				<?php
				}
				?>
			</td>
			<th class="small text-uppercase font-weight-bold text-muted">
				Discharged
			</th>
			<td>
				<?php
				if (!empty($data->case->discharge_date)) {
					echo $data->case->discharge_date->format($dateFormat);
				} else {
				?>
					<div class="text-muted text-uppercase">
						&mdash;
					</div>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th class="small text-uppercase font-weight-bold text-muted">
				Insurance
			</th>
			<td>
				<?php
				if ($data->case->insurance_provider && $data->case->insurance_provider->name) {
					echo $data->case->insurance_provider->name;
				} else {
				?>
					<div class="text-muted text-uppercase">
						&mdash;
					</div>
				<?php
				}
				?>
			</td>
			<th class="small text-uppercase font-weight-bold text-muted">
				Plan
			</th>
			<td>
				<?php
				if ($data->case->insurance_plan) {
				?>
					<div>
						<?php
						echo $data->case->insurance_plan;
						?>
					</div>
				<?php
				} else {
				?>
					<div class="text-muted text-uppercase">
						&mdash;
					</div>
				<?php
				}

				if ($data->case->insurance_number && $data->case->insurance_number) {
				?>
					<div>
						<?php
						echo '#' . $data->case->insurance_number;
						?>
					</div>
				<?php
				}
				?>
			</td>
		</tr>
	</table>

	<!-- Body -->
	<table class="w-100 mb-4" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%">
				<?php
				if (empty($letter)) {
				?>
					<p class="alert bg-light text-muted">
						No response letter was provided.
					</p>
				<?php
				} else {
				?>
					<?= nl2br($letter, true); ?>
				<?php
				}
				?>
			</td>
		</tr>
	</table>
</main>
