<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class LoginController extends Controller {

    public function singin() {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('singin', [
            'flash' => $flash
        ]);
    }

    public function singinAction(){
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if($email && $password) {
            $token = UserHandler::verifyLogin($email, $password);
            if($token) {
                $_SESSION['token'] = $token;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail e/ou senha não conferem.';
                $this->redirect('/login');
            }
        } else {
            $this->redirect('/login');
        }
    }

    public function singup() {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('singup', [
            'flash' => $flash
        ]);
    }
    public function singupAction() {
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
        $birthdate = filter_input(INPUT_POST, 'birthdate');

        if($name && $email && $password && $birthdate) {
            $birthdate = explode('/', $birthdate);
            if(count($birthdate) != 3) {
                $_SESSION['flash'] = 'Data de nascimento invalida.';
                $this->redirect('/cadastro');
            }
            
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            if(strtotime($birthdate === false)) {
                $_SESSION['flash'] = 'Data de nascimento invalida.';
                $this->redirect('/cadastro');
                }

                if(UserHandler::emailExists($email) === false) {
                   $token = UserHandler::addUser($name, $email, $password, $birthdate);
                   $_SESSION['token'] = $token;
                   $this->redirect('/');
                } else { 
                    $_SESSION['flash'] = 'Já tem uma conta cadastrada com o e-mail inserido.';
                }

        } else {
            $_SESSION['flash'] = 'Preencha todos os campos.';
            $this->redirect('/cadastro');
        }
    }

    public function logout() {
        $_SESSION['token'] = '';
        $this->redirect('/login');
    }
    
}