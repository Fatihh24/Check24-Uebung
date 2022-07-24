<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Form;
use Laminas\Form\Element;

class CreateForm extends Form {
    public function __construct() {
        parent::__construct('new_account');
        $this->setAttribute('method','post');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'username',
            'options' => [
                'label' => 'Username'
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 25,
                'pattern' => '^[a-zA-Z0-9]+$',
                'data-toggle' => 'tooltip',
                'class' => 'form-control',
                'title' => 'Username muss Alphanumerische Zeichen beinhalten',
                'placeholder' => 'Username eingeben'
            ]
        ]);

        $this->add([
			'type' => Element\Email::class,
			'name' => 'email',
			'options' => [
				'label' => 'Email Addresse'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 128,
				'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'title' => 'Provide a valid and working email address',
				'placeholder' => 'Email Addresse eingeben'
			]
		]);

        $this->add([
			'type' => Element\Password::class,
			'name' => 'password',
			'options' => [
				'label' => 'Passwort'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',   # styling
				'title' => 'Password must have between 8 and 25 characters',
				'placeholder' => 'Passwort eingeben'
			]
		]);

        $this->add([
			'type' => Element\Password::class,
			'name' => 'confirm_password',
			'options' => [
				'label' => 'Passwort wiederholen'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',   # styling
				'title' => 'Password must match that provided above',
				'placeholder' => 'Password Eingabe wiederholen'
			]
		]);

        $this->add([
			'type' => Element\Submit::class,
			'name' => 'create_account',
			'attributes' => [
				'value' => 'Account erstellen',
				'class' => 'btn btn-primary'
			]
		]);
    }
}