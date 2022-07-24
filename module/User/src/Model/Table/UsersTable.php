<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\InputFilter;
use Laminas\Validator;
use Laminas\I18n;
use User\Model\Entity\UserEntity;
use Laminas\Hydrator\ClassMethodsHydrator;

class UsersTable extends AbstractTableGateway {
    protected $adapter;          
	protected $table = 'users';

    public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->initialize();
	}

	public function fetchAccountByEmail(string $email)
	{
		$sqlQuery = $this->sql->select()
			->where(['email' => $email]);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();

		if(!$handler) {
			return null;
		}

		$classMethod = new ClassMethodsHydrator();
		$entity      = new UserEntity();
		$classMethod->hydrate($handler, $entity);

		return $entity;
	}

	public function getLoginFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory = new InputFilter\Factory();

		# filter and validate email 
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'email',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class],  # removes html tags
						['name' => Filter\StringTrim::class],
						//['name' => Filter\StringToLower::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class],
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 6,  
								'max' => 128,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Email address must have at least 6 characters!',
									Validator\StringLength::TOO_LONG => 'Email address must have at most 128 characters!',
								],
							],
						],
						['name' => Validator\EmailAddress::class],
						[
							'name' => Validator\Db\RecordExists::class,
							'options' => [
								'table' => $this->table,
								'field' => 'email',
								'adapter' => $this->adapter,
							],
						],
					],
				]
			)
		);

		# filter and validate password
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'password',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class],  # removes html tags
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class],
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 8,
								'max' => 25,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters!',
									Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters!',
								],
							],
						],
					],
				]
			)
		);
		# be sure to return the input filter
		return $inputFilter;
	}

	public function getCreateFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory = new InputFilter\Factory();

		# filter and validate username input field
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'username',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class], # stips html tags
						['name' => Filter\StringTrim::class], # removes empty spaces
						['name' => I18n\Filter\Alnum::class], # allows only [a-zA-Z0-9] characters
					],
					'validators' => [
						['name' => Validator\NotEmpty::class],
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 2,
								'max' => 25,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Username must have at least 2 characters',
									Validator\StringLength::TOO_LONG => 'Username must have at most 25 characters',
								],
							],
						], 
						[
							'name' => I18n\Validator\Alnum::class,
							'options' => [
								'messages' => [
									I18n\Validator\Alnum::NOT_ALNUM => 'Username must consist of alphanumeric characters only',
								],
							],
						],
						[
							'name' => Validator\Db\NoRecordExists::class,
							'options' => [
								'table' => $this->table, 
								'field' => 'username',
								'adapter' => $this->adapter,
							],
						],
					],
				]
			)
		);

		# filter and validate email input field
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'email',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class],
						['name' => Filter\StringTrim::class], 
					],
					'validators' => [
						['name' => Validator\NotEmpty::class],
						['name' => Validator\EmailAddress::class],
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 6,
								'max' => 128,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Email address must have at least 6 characters',
									Validator\StringLength::TOO_LONG => 'Email address must have at most 128 characters',
								],
							],
						],
						[
							'name' => Validator\Db\NoRecordExists::class,
							'options' => [
								'table' => $this->table,
								'field' => 'email',
								'adapter' => $this->adapter,
							],
						],
					],
				]
			)
		);

		# filter and validate password input field
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'password',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class], # stips html tags
						['name' => Filter\StringTrim::class], # removes empty spaces
					],
					'validators' => [
						['name' => Validator\NotEmpty::class],
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 8,
								'max' => 25,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
									Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
								],
							],
						],
					],
				]
			)
		);

		# filter and validate confirm_password field
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'confirm_password',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class], # stips html tags
						['name' => Filter\StringTrim::class], # removes empty spaces
					],
					'validators' => [
						['name' => Validator\NotEmpty::class], 
						[
							'name' => Validator\StringLength::class,
							'options' => [
								'min' => 8,
								'max' => 25,
								'messages' => [
									Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
									Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters',
								],
							],
						],
						[
							'name' => Validator\Identical::class,
							'options' => [
								'token' => 'password',
								'messages' => [
									Validator\Identical::NOT_SAME => 'Passwords do not match!',
								],
							],
						],
					],
				]
			)
		);

		return $inputFilter;
	}

	public function saveAccount(array $data)
	{
		$values = [
			'username' => ucfirst($data['username']),
			'email'    => mb_strtolower($data['email']),
			'password' => $data['password'], 
		];

		$sqlQuery = $this->sql->insert()->values($values); 
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}
}