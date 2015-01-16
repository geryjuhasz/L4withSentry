<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateNewGroupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:createNewGroup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Use this to create new user group. For permissions use any of these (Superadmin, Admin, Client, User)';

	protected $_aPossiblePermissions = array(
		'Superadmin',
		'Admin',
		'Client',
		'User',
	);

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$aParams = array_merge(
			array(
				'name' => $this->argument('name'),
			),
			array(
				'permissions' => $this->argument('permissions')
			)
		);
		$this->_createGroup($aParams);
	}

	protected function _createGroup($aParams) {
		try
		{
			$aPermissions = array();
			foreach ($aParams['permissions'] as $sPermission) {
				if (in_array($sPermission, $this->_aPossiblePermissions)) {
					$aPermissions = array_merge($aPermissions, array($sPermission => 1));
				}
			}

			// Create the group
			$oGroup = Sentry::createGroup(array(
				'name'        => $aParams['name'],
				'permissions' => $aPermissions,
			));

			if ($oGroup->exists) {
				$this->info('Saved succesfully!');
			} else {
				$this->error('Something went wrong!');
			}
		}
		catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
		{
			echo 'Name field is required';
		}
		catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
		{
			echo 'Group already exists';
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'Name of the group'),
			array('permissions', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Group permissions'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
