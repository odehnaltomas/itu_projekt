<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


final class SignInFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('login', 'Přihlašovací jméno:')
			->setHtmlAttribute('placeholder', 'Přihlašovací jméno')
			->setHtmlAttribute('autofocus')
			->setRequired('Prosím zadejte své přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setHtmlAttribute('placeholder', 'Heslo')
			->setRequired('Prosím zadejte své heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si mě');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
				$this->user->login($values->login, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Nesprávné jméno nebo heslo.');
				return;
			}
			$onSuccess();
		};

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;

		$form->getElementPrototype()->class('dd');
		foreach ($form->getControls() as $control) {
			if ($control instanceof Nette\Forms\Controls\Button) {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-lg btn-primary btn-block' : 'btn btn-default');
				$usedPrimary = TRUE;
			} elseif ($control instanceof Nette\Forms\Controls\TextBase || $control instanceof Nette\Forms\Controls\SelectBox || $control instanceof Nette\Forms\Controls\MultiSelectBox) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($control instanceof Nette\Forms\Controls\Checkbox || $control instanceof Nette\Forms\Controls\CheckboxList || $control instanceof Nette\Forms\Controls\RadioList) {
				$control->getSeparatorPrototype()->setName('div')->addClass('checkbox mb-3');
			}
		}

		return $form;
	}
}
