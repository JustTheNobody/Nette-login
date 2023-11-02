<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\AppModel;
use Nette\Application\UI\Form;
use App\Models\UsersRepository;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

class FrontendPresenter extends Presenter
{
    /** @var Orm */
    protected $orm;

    /** @var UsersRepository */
    protected UsersRepository $users;

    public function injectFrontendDependencies( AppModel $orm ) : void
    {
        $this->orm = $orm;
        $this->users = $this->orm->getRepository( UsersRepository::class );
    }

    protected function createComponentSignInForm() : Form
    {
        $form = new Form;

        $form->addProtection( 'protected_try_again' );

        $form->addText( 'email', 'username' )
            ->setRequired( 'username_required' );

        $form->addPassword( 'password', 'password' )
            ->setRequired( 'password_required' );

        $form->addCheckbox( 'remember', 'remember' )
            ->setDefaultValue( true );

        $form->addSubmit( 'sign', 'signin' );

        $form->onSuccess[] = array( $this, 'signInFormSucceeded' );

        return $form;
    }

    public function signInFormSucceeded( Form $form ) : void
    {
        $values = $form->getValues();

        try {
            $this->user->login($values->email, $values->password);
            $this->redirect('Admin:default');
        } catch (AuthenticationException $e) {

            $this->flashMessage('Incorrect credentials');
            $this->template->signWindowMessage = 'Login failed: Incorrect credentials';
            $this->redrawControl( 'signWindowSnippet' );
        }

    }

    public function handleLogOut()
    {
        $this->user->logout();
        $this->redirect('Home:default');
    }
}
