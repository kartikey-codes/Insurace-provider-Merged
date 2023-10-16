<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 * @property \App\View\Helper\PhoneHelper $Phone
 * @property \App\View\Helper\MixHelper $Mix
 */
class AppView extends View
{
	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading helpers.
	 *
	 * e.g. `$this->loadHelper('Html');`
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		$this->loadHelper('Pdf');
		$this->loadHelper('Phone');

		$this->loadHelper('Form', [
			'errorClass' => 'is-invalid',
			'typeMap' => [
				'string' => 'text',
				'datetime' => 'datetime',
				'boolean' => 'checkbox',
				'timestamp' => 'datetime',
				'text' => 'textarea',
				'time' => 'time',
				'date' => 'date',
				'float' => 'number',
				'integer' => 'number',
				'decimal' => 'number',
				'binary' => 'file',
				'uuid' => 'string',
			],
			'templates' => [
				'button' => '<button{{attrs}}>{{text}}</button>',

				'checkbox' => '<input type="checkbox" class="form-check-input{{attrs.class}}" name="{{name}}" value="{{value}}"{{attrs}}>',
				'checkboxFormGroup' => '{{label}}',
				'checkboxWrapper' => '<div class="form-check">{{label}}</div>',
				'checkboxContainer' => '<div class="form-check checkbox{{required}}">{{content}}</div>',
				'checkboxContainerHorizontal' => '<div class="form-group row"><div class="{{labelColumnClass}}"></div><div class="{{inputColumnClass}}"><div class="form-check checkbox{{required}}">{{content}}</div></div></div>',
				'multicheckboxContainer' => '<fieldset class="form-group {{type}}{{required}}">{{content}}</fieldset>',
				'multicheckboxContainerHorizontal' => '<fieldset class="form-group {{type}}{{required}}"><div class="row">{{content}}</div></fieldset>', 'dateWidget' => '<div class="row">{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}</div>',

				//'input'  => '<input type="{{type}}" name="{{name}}" class="form-control {{error}}" {{attrs}} />',
				// 'input'  => '<div class="{{divClass}}"><input type="{{type}}" name="{{name}}"{{attrs}}" /></div>',
				// 'select' => '<div class="{{divClass}}"><select name="{{name}}"{{attrs}} class="form-control">{{content}}</select></div>',
				'inputContainer' => '<div class="form-group {{type}}{{required}}">{{content}}</div>',
				'inputContainerError' => '<div class="form-group {{type}}{{required}} is-invalid">{{content}}{{error}}</div>',
				'error' => '<div class="invalid-feedback">{{content}}</div>',

				// 'radio' => '<input type="radio" class="form-check-input{{attrs.class}}" name="{{name}}" value="{{value}}"{{attrs}}>',
				// 'radioWrapper' => '<div class="form-check">{{label}}</div>',
				// 'radioContainer' => '<fieldset class="form-group {{type}}{{required}}">{{content}}</fieldset>',
				// 'radioContainerHorizontal' => '<fieldset class="form-group {{type}}{{required}}"><div class="row">{{content}}</div></fieldset>',
			],
		]);
	}
}
