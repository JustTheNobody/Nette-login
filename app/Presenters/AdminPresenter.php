<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Exception;
use App\Models\User;
use Nette\Security\Passwords;
use Nette\Application\UI\Form;

class AdminPresenter extends FrontendPresenter
{
    protected int $paginator;

    protected $temp;

    /** limit of users at page */
    protected int $limit = 4;

    public function startup()
    {
        parent::startup();
        if( !$this->user->isLoggedIn()){
            $this->flashMessage('You have to log in first', 'error');
            $this->redirect('Home:default');
        }

    }

    public function actionDefault( $page = 0 )
    {

        $offset     = $page * $this->limit;

        $allUsers        = $this->users->findAll();
        $this->paginator = $allUsers->countStored();

        $this->template->pagesCount  = floor($this->paginator / $this->limit);
        $this->template->currentPage = $page;
        $this->template->allUsers    = $allUsers->limitBy($this->limit, $offset);
    }

    public function actionEdit( $id )
    {
        $this->temp = $this->users->getById( $id );
    }

    public function handleDelete( $id )
    {
        $user = $this->users->getById($id);

        if ($user) {
            $this->users->remove( $user );
            $this->users->flush();
            $this->flashMessage('User deleted successfully.', 'success');
        } else {
            $this->flashMessage('User not found.', 'error');
        }

        $this->redrawControl( 'usersTable' );
        $this->redrawControl( 'messages' );
    }

    protected function createComponentEditForm() : Form
    {
        $form = new Form;

        $form->addProtection( 'protected_try_again' );

        $form->addHidden( 'id', 'id' )
            ->setValue($this->temp->id);

        $form->addText( 'email', 'email' )
            ->addRule( Form::EMAIL, 'email_not_valid' )
            ->setValue($this->temp->email)
            ->setRequired( 'username_required' );

        $form->addPassword( 'password', 'password' )
            ->addRule( Form::MIN_LENGTH, 'min_password', 6 )
            ->setValue($this->temp->password);

        $form->addCheckbox( 'remember', 'remember' )
            ->setDefaultValue( true );

        $form->addSubmit( 'update', 'update' );

        $form->onSuccess[] = array( $this, 'editFormSucceeded' );

        return $form;
    }

    public function editFormSucceeded( Form $form ) : void
    {
        $values = $form->getValues();

        $password = new Nette\Security\Passwords;
        $newUser = $this->users->getById( $values['id'] );
        if( isset($values['password']) && !empty($values['password']) )
            $newUser->password  = $password->hash( $values['password'] );
        $newUser->email     = $values['email'];

        try
        {
            $this->users->persistAndFlush($newUser);
            $this->flashMessage('User created successfully.');
        }
        catch ( Exception $e )
        {
            $this->flashMessage('Incorrect credentials');
            $this->template->signWindowMessage = 'Login failed: Incorrect credentials';
            $this->redrawControl( 'editWindowSnippet' );
        }

        $this->redirect(':Admin:default');
    }
}