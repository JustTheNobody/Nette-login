<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Exception;
use App\Models\User;
use Nette\Application\UI\Form;

final class RegisterPresenter extends FrontendPresenter
{
    protected function createComponentRegisterForm() : Form
    {
        $form = new Form;

        $form->addProtection( 'protected_try_again' );

        $form->addText( 'email', 'email' )
            ->addRule( Form::EMAIL, 'email_not_valid' )
            ->setRequired( 'username_required' );

        $form->addPassword( 'password', 'password' )
            ->addRule( Form::MIN_LENGTH, 'min_password', 6 )
            ->addRule( Form::FILLED, 'field_required' );

        $form->addText( 'name', 'name' )
            ->setRequired( 'username_required' );

        $form->addText( 'surname', 'surname' )
            ->setRequired( 'username_required' );


        $form->addCheckbox( 'remember', 'remember' )
            ->setDefaultValue( true );

        $form->addSubmit( 'register', 'register' );
        $form->onSuccess[] = array( $this, 'registerFormSucceeded' );

        return $form;
    }

    public function registerFormSucceeded( Form $form ) : void
    {
        $values   = $form->getValues();
        $newUser  = new User();
        $password = new Nette\Security\Passwords;

        $newUser->password  = $password->hash( $values['password'] );;
        $newUser->email     = $values['email'];
        $newUser->name      = $values['name'];
        $newUser->surname   = $values['surname'];

        try
        {
            $this->users->persistAndFlush($newUser);
            $this->flashMessage('User created successfully.');
        }
        catch ( Exception $e )
        {
            $this->flashMessage( $e->getMessage() );
            $this->redirect('this');
        }

        $this->redirect(':Home:default');
    }
}
