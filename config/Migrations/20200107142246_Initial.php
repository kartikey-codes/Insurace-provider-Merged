<?php

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
	public function up()
	{
		$this->table('agencies')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('state', 'string', [
				'default' => null,
				'limit' => 2,
				'null' => true,
			])
			->addColumn('zip', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addColumn('division', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_name', 'string', [
				'default' => null,
				'limit' => 150,
				'null' => true,
			])
			->addColumn('contact_title', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_direct_line', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('alert_reasons')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('appeal_hearings')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('hearing_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('start_time', 'time', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('end_time', 'time', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('rescheduled', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('reschedule_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('reschedule_start_time', 'time', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('reschedule_end_time', 'time', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('judge_name', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('court', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('call_in_phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('appeal_letter_items')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_template_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_template_item_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('body', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'appeal_template_id',
				]
			)
			->addIndex(
				[
					'appeal_template_item_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('appeal_levels')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('allow_hearing_date', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'allow_hearing_date',
				]
			)
			->create();

		$this->table('appeal_not_defendable_reasons', ['id' => false, 'primary_key' => ['appeal_id', 'not_defendable_reason_id']])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('not_defendable_reason_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'not_defendable_reason_id',
				]
			)
			->create();

		$this->table('appeal_notes')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('subject', 'string', [
				'default' => null,
				'limit' => 200,
				'null' => true,
			])
			->addColumn('notes', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('appeal_reference_numbers')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('reference_number_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('value', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'reference_number_id',
				]
			)
			->create();

		$this->table('appeal_template_items')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_template_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('section_name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('display_header', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('require_determination', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('instructions', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('order_number', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('default_text', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'display_header',
				]
			)
			->addIndex(
				[
					'appeal_template_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'require_determination',
				]
			)
			->create();

		$this->table('appeal_templates')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('appeal_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('appeals')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_level_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_template_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('defendable', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('letter_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('received_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('due_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('payor_due_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('hearing_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('hearing_time', 'time', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('days_to_respond', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('days_to_respond_from_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('priority', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('letter_footnotes', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('current_status_due_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned_to', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('initial_reviewed_first', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('initial_reviewed_first_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('initial_reviewed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('initial_reviewed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('determination_first', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('determination_first_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('determination', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('determination_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('final_reviewed_first', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('final_reviewed_first_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('final_reviewed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('final_reviewed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('completed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'defendable',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'agency_id',
				]
			)
			->addIndex(
				[
					'appeal_level_id',
				]
			)
			->addIndex(
				[
					'appeal_type_id',
				]
			)
			->addIndex(
				[
					'assigned_to',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'completed_by',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'determination_by',
				]
			)
			->addIndex(
				[
					'final_reviewed_by',
				]
			)
			->addIndex(
				[
					'initial_reviewed_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'hearing_date',
				]
			)
			->create();

		$this->table('blurb_categories')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('blurb_footnotes')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('blurb_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('body', 'string', [
				'default' => null,
				'limit' => 1024,
				'null' => true,
			])
			->addColumn('order_number', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'blurb_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('blurb_tags', ['id' => false, 'primary_key' => ['blurb_id', 'tag_id']])
			->addColumn('blurb_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('tag_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();

		$this->table('blurbs')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('blurb_category_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('name_short', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('body', 'string', [
				'default' => null,
				'limit' => 4000,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'blurb_category_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('calendar_events')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => false,
			])
			->addColumn('start_date', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addColumn('end_date', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('all_day', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('case_activity', ['id' => false, 'primary_key' => ['case_id', 'user_id']])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->create();

		$this->table('case_denial_reasons')
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('denial_reason_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'denial_reason_id',
				]
			)
			->create();

		$this->table('case_evidence_criteria', ['id' => false, 'primary_key' => ['case_id', 'evidence_criteria_id']])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('evidence_criteria_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'evidence_criteria_id',
				]
			)
			->create();

		$this->table('case_outcomes')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('case_readmissions')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('visit_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('admit_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('discharge_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'admit_date',
					'discharge_date',
				]
			)
			->addIndex(
				[
					'visit_number',
				]
			)
			->create();

		$this->table('case_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->create();

		$this->table('case_umt_answers')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('utilization_management_tool_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('meets_criteria', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('result', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('details', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'meets_criteria',
				]
			)
			->addIndex(
				[
					'utilization_management_tool_id',
				]
			)
			->create();

		$this->table('cases')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('patient_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('denial_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_outcome_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_plan', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('meets_primary_umt_criteria', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('meets_secondary_umt_criteria', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('total_claim_amount', 'decimal', [
				'default' => null,
				'null' => true,
				'precision' => 19,
				'scale' => 4,
			])
			->addColumn('reimbursement_amount', 'decimal', [
				'default' => null,
				'null' => true,
				'precision' => 19,
				'scale' => 4,
			])
			->addColumn('disputed_amount', 'decimal', [
				'default' => null,
				'null' => true,
				'precision' => 19,
				'scale' => 4,
			])
			->addColumn('settled_amount', 'decimal', [
				'default' => null,
				'null' => true,
				'precision' => 19,
				'scale' => 4,
			])
			->addColumn('insurance_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('visit_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('admit_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('discharge_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('closed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('closed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('assigned', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned_to', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'case_outcome_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'denial_type_id',
				]
			)
			->addIndex(
				[
					'facility_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->addIndex(
				[
					'insurance_type_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'patient_id',
				]
			)
			->addIndex(
				[
					'meets_primary_umt_criteria',
				]
			)
			->addIndex(
				[
					'meets_secondary_umt_criteria',
				]
			)
			->addIndex(
				[
					'admit_date',
					'discharge_date',
				]
			)
			->addIndex(
				[
					'visit_number',
				]
			)
			->create();

		$this->table('client_employee_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('client_employees')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('client_employee_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('first_name', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => false,
			])
			->addColumn('last_name', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => true,
			])
			->addColumn('title', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => true,
			])
			->addColumn('work_phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mobile_phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('emails_active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'client_employee_type_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'emails_active',
				]
			)
			->addIndex(
				[
					'facility_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('clients')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('company_messages')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('body', 'text', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('days_to_respond_froms')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => false,
			])
			->create();

		$this->table('denial_reasons')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => false,
			])
			->create();

		$this->table('denial_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('multiple_service_dates', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'multiple_service_dates',
				]
			)
			->create();

		$this->table('evidence_criteria')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('facilities')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('parent_company', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('state', 'string', [
				'default' => null,
				'limit' => 2,
				'null' => true,
			])
			->addColumn('zip', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'client_id',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'facility_type_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('facility_faxes')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'facility_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('facility_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('guidelines')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('utilization_management_tool_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->addIndex(
				[
					'insurance_type_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'utilization_management_tool_id',
				]
			)
			->create();

		$this->table('incoming_documents')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('sender_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('file_name', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('pages', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('provider_identifier', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('acknowledgement_sent', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned_to', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'assigned_to',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'facility_id',
				]
			)
			->addIndex(
				[
					'provider_identifier',
				]
			)
			->create();

		$this->table('insurance_provider_agencies')
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'agency_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->create();

		$this->table('insurance_provider_appeal_levels')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_level_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_level_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->create();

		$this->table('insurance_provider_insurance_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('insurance_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->addIndex(
				[
					'insurance_type_id',
				]
			)
			->create();

		$this->table('insurance_provider_opportunities')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_opportunity_set_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_level_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('days_to_respond', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('number', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'agency_id',
				]
			)
			->addIndex(
				[
					'appeal_level_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_opportunity_set_id',
				]
			)
			->addIndex(
				[
					'number',
				]
			)
			->create();

		$this->table('insurance_provider_opportunity_sets')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('effective_date', 'date', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addIndex(
				[
					'effective_date',
				]
			)
			->create();

		$this->table('insurance_providers')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('default_insurance_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('utilization_management_tool_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('state', 'string', [
				'default' => null,
				'limit' => 2,
				'null' => true,
			])
			->addColumn('zip', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('require_primary_umt_answers', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('require_secondary_umt_answers', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'default_insurance_type_id',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('insurance_types')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('use_provider_guidelines', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'use_provider_guidelines',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->create();

		$this->table('messages')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('from_user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('to_user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('body', 'text', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addColumn('read_at', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'from_user_id',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'to_user_id',
				]
			)
			->addIndex(
				[
					'read_at',
				]
			)
			->create();

		$this->table('not_defendable_reasons')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('denial_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'denial_type_id',
				]
			)
			->create();

		$this->table('outgoing_documents')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('parent_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('patient_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('filename', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('provider_identifier', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('cancelled', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('cancelled_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('processed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('failed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('status_message', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addIndex(
				[
					'cancelled',
				]
			)
			->addIndex(
				[
					'completed',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'failed',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'cancelled_by',
				]
			)
			->addIndex(
				[
					'facility_id',
				]
			)
			->addIndex(
				[
					'parent_id',
				]
			)
			->addIndex(
				[
					'patient_id',
				]
			)
			->addIndex(
				[
					'provider_identifier',
				]
			)
			->create();

		$this->table('patients')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('first_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('middle_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('last_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('date_of_birth', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('secured', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('social', 'string', [
				'default' => null,
				'limit' => 16,
				'null' => true,
			])
			->addColumn('sex', 'string', [
				'default' => null,
				'limit' => 16,
				'null' => true,
			])
			->addColumn('marital_status', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('state', 'string', [
				'default' => null,
				'limit' => 2,
				'null' => true,
			])
			->addColumn('zip', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'secured',
				]
			)
			->create();

		$this->table('permissions')
			->addColumn('controller', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('action', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => false,
			])
			->addIndex(
				[
					'controller',
				]
			)
			->addIndex(
				[
					'name',
				]
			)
			->create();

		$this->table('reference_numbers')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('roles')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'name',
				]
			)
			->create();

		$this->table('roles_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission_id']])
			->addColumn('role_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('permission_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();

		$this->table('tags')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->create();

		$this->table('user_alerts')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'user_id',
				],
				['unique' => true]
			)
			->addIndex(
				[
					'active',
				]
			)
			->create();

		$this->table('user_logins')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('username', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addColumn('logout', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('success', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('details', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('ip_address', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('hostname', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('user_agent', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'user_id',
				]
			)
			->addIndex(
				[
					'logout',
				]
			)
			->addIndex(
				[
					'success',
				]
			)
			->create();

		$this->table('users')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('first_name', 'string', [
				'default' => null,
				'limit' => 48,
				'null' => true,
			])
			->addColumn('middle_name', 'string', [
				'default' => null,
				'limit' => 48,
				'null' => true,
			])
			->addColumn('last_name', 'string', [
				'default' => null,
				'limit' => 48,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => false,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('admin', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('date_of_birth', 'date', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('username', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('password', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addColumn('password_changed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('password_reset_token', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->addColumn('password_reset_token_expiration', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('api_token', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('last_login', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('last_login_ip', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('last_seen', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('locked', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('lock_expiration', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex(
				[
					'email',
				],
				['unique' => true]
			)
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'admin',
				]
			)
			->addIndex(
				[
					'api_token',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->addIndex(
				[
					'locked',
				]
			)
			->addIndex(
				[
					'username',
					'password',
				]
			)
			->create();

		$this->table('users_roles', ['id' => false, 'primary_key' => ['user_id', 'role_id']])
			->addColumn('user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('role_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();

		$this->table('utilization_management_tools')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 32,
				'null' => false,
			])
			->addColumn('url', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'created_by',
				]
			)
			->addIndex(
				[
					'deleted_by',
				]
			)
			->addIndex(
				[
					'modified_by',
				]
			)
			->create();

		$this->table('withdrawn_reasons')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->create();
	}

	public function down()
	{
		$this->table('agencies')->drop()->save();
		$this->table('alert_reasons')->drop()->save();
		$this->table('appeal_hearings')->drop()->save();
		$this->table('appeal_letter_items')->drop()->save();
		$this->table('appeal_levels')->drop()->save();
		$this->table('appeal_not_defendable_reasons')->drop()->save();
		$this->table('appeal_notes')->drop()->save();
		$this->table('appeal_reference_numbers')->drop()->save();
		$this->table('appeal_template_items')->drop()->save();
		$this->table('appeal_templates')->drop()->save();
		$this->table('appeal_types')->drop()->save();
		$this->table('appeals')->drop()->save();
		$this->table('blurb_categories')->drop()->save();
		$this->table('blurb_footnotes')->drop()->save();
		$this->table('blurb_tags')->drop()->save();
		$this->table('blurbs')->drop()->save();
		$this->table('calendar_events')->drop()->save();
		$this->table('case_activity')->drop()->save();
		$this->table('case_denial_reasons')->drop()->save();
		$this->table('case_evidence_criteria')->drop()->save();
		$this->table('case_outcomes')->drop()->save();
		$this->table('case_readmissions')->drop()->save();
		$this->table('case_types')->drop()->save();
		$this->table('case_umt_answers')->drop()->save();
		$this->table('cases')->drop()->save();
		$this->table('client_employee_types')->drop()->save();
		$this->table('client_employees')->drop()->save();
		$this->table('clients')->drop()->save();
		$this->table('company_messages')->drop()->save();
		$this->table('days_to_respond_froms')->drop()->save();
		$this->table('denial_reasons')->drop()->save();
		$this->table('denial_types')->drop()->save();
		$this->table('evidence_criteria')->drop()->save();
		$this->table('facilities')->drop()->save();
		$this->table('facility_faxes')->drop()->save();
		$this->table('facility_types')->drop()->save();
		$this->table('guidelines')->drop()->save();
		$this->table('incoming_documents')->drop()->save();
		$this->table('insurance_provider_agencies')->drop()->save();
		$this->table('insurance_provider_appeal_levels')->drop()->save();
		$this->table('insurance_provider_insurance_types')->drop()->save();
		$this->table('insurance_provider_opportunities')->drop()->save();
		$this->table('insurance_provider_opportunity_sets')->drop()->save();
		$this->table('insurance_providers')->drop()->save();
		$this->table('insurance_types')->drop()->save();
		$this->table('messages')->drop()->save();
		$this->table('not_defendable_reasons')->drop()->save();
		$this->table('outgoing_documents')->drop()->save();
		$this->table('patients')->drop()->save();
		$this->table('permissions')->drop()->save();
		$this->table('reference_numbers')->drop()->save();
		$this->table('roles')->drop()->save();
		$this->table('roles_permissions')->drop()->save();
		$this->table('tags')->drop()->save();
		$this->table('user_alerts')->drop()->save();
		$this->table('user_logins')->drop()->save();
		$this->table('users')->drop()->save();
		$this->table('users_roles')->drop()->save();
		$this->table('utilization_management_tools')->drop()->save();
		$this->table('withdrawn_reasons')->drop()->save();
	}
}
