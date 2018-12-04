<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;


final class SignUpFormFactory
{
	use Nette\SmartObject;

	const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('login', 'Přihlašovací jméno:')
			->setHtmlAttribute('placeholder', 'Přihlašovací jméno')
			->setRequired('Prosím zadejte své přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setHtmlAttribute('placeholder', 'Heslo')
			->setRequired('Prosím zadejte své heslo.');

		$form->addPassword('password_again', 'Heslo znovu:')
			->setHtmlAttribute('placeholder', 'Heslo znovu')
			->setRequired('Prosím zadejte své heslo.');

		$form->addSubmit('send', 'Registrovat');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			if ($values['password'] !== $values['password_again']) {
				$form['password_again']->addError('Hesla se musí shodovat!');
				return;
			}
			try {
				$this->userManager->add($values->login, $values->password);
			} catch (Model\DuplicateNameException $e) {
				$form['login']->addError('Přihlšovací jméno je již obsazeno.');
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
