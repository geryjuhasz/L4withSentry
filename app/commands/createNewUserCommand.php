<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateNewUserCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:createNewUser';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create New User.';

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
				'email' => $this->argument('email'),
			),
			array(
				'password' => $this->argument('password')
			),
			array(
				'group_id' => $this->argument('group_id')
			)
		);
		$this->_createUser($aParams);
	}

	protected function _createUser($aParams) {
		try
		{
			// Create the user
			$user = Sentry::createUser(array(
				'email'     => $aParams['email'],
				'password'  => $aParams['password'],
				'activated' => TRUE,
			));

			// Find the group using the group id
			$adminGroup = Sentry::findGroupById($aParams['group_id']);

			// Assign the group to the user
			$user->addGroup($adminGroup);
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
			echo 'Login field is required.';
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
			echo 'Password field is required.';
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
			echo 'User with this login already exists.';
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			echo 'Group was not found.';
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
			array('email', InputArgument::REQUIRED, 'Email of the user'),
			array('password', InputArgument::REQUIRED, 'Password of the user'),
			array('group_id', InputArgument::REQUIRED, 'Group id of the user'),
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
