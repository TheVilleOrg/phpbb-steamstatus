<?php

namespace stevotvr\steamstatus\migrations;

class steamid_profile_field extends \phpbb\db\migration\profilefield_base_migration
{
	protected $profilefield_name = 'steam_id';
	protected $profilefield_database_type = array('VCHAR', '');
	protected $profilefield_data = array(
		'field_name'			=> 'steam_id',
		'field_type'			=> 'profilefields.type.string',
		'field_ident'			=> 'steam_id',
		'field_length'			=> '40',
		'field_minlen'			=> '0',
		'field_maxlen'			=> '255',
		'field_novalue'			=> '',
		'field_default_value'	=> '',
		'field_validation'		=> '.+',
		'field_required'		=> 0,
		'field_show_novalue'	=> 0,
		'field_show_on_reg'		=> 1,
		'field_show_on_pm'		=> 0,
		'field_show_on_vt'		=> 1,
		'field_show_profile'	=> 1,
		'field_hide'			=> 0,
		'field_no_view'			=> 0,
		'field_active'			=> 0,
	);
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'create_custom_field'))),
		);
	}
}
