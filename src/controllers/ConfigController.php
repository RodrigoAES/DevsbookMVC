<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class ConfigController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if($this->loggedUser === false){
            $this->redirect('/login');
        }
    }

    public function index() {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
    
        
        //Pegando informações do usuario
        $user = UserHandler::getUser($this->loggedUser->id, false);
        if(!$user){
            $this->redirect('/');
        }

        $birthdate = explode('-', $user->birthdate);
        $birthdate = $birthdate[2].'/'.$birthdate[1].'/'.$birthdate[0];

        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'birthdate' => $birthdate,
            'flash' => $flash
        ]);
    }

    public function update() {
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $email = filter_input(INPUT_POST, 'email');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        $newPassword = filter_input(INPUT_POST, 'newPassword');
        $newPasswordConfirm = filter_input(INPUT_POST, 'newPasswordConfirm');
        $password = filter_input(INPUT_POST, 'password');

        

        $birthdate = explode('/', $birthdate);
        if(count($birthdate) != 3) {
            $_SESSION['flash'] = 'Data de nascimento invalida.';
            $this->redirect('/config');
        }
        $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
        if(strtotime($birthdate === false)) {
            $_SESSION['flash'] = 'Data de nascimento invalida.';
            $this->redirect('/config');
            }
        

        if(UserHandler::verifyPassword($this->loggedUser->id, $password)) {
            if($newPassword){
                if(strcmp($newPassword, $newPasswordConfirm) != 0){
                    $_SESSION['flash'] = 'Senhas não coincidem.';
                    $this->redirect('/config');
                }
                if(strcmp($newPassword, $password) === 0){
                    $_SESSION['flash'] = 'Você não pode redefinir a senha com a anterior.';
                    $this->redirect('/config');
                }
            }

            if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $newAvatar = $_FILES['avatar'];

                if(in_array($newAvatar['type'], ['image/jpeg', 'image.jpg', 'image/png'])) {
                    $newAvatar = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                }
            }

            if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
                $newCover = $_FILES['cover'];

                if(in_array($newCover['type'], ['image/jpeg', 'image.jpg', 'image/png'])) {
                    $newCover = $this->cutImage($newCover, 850, 310, 'media/covers');
                }
            }

            $updateInputs = [
                'name' => $name,
                'email' => $email,
                'birthdate' => $birthdate,
                'city' => $city,
                'work' => $work,
                'newPassword' => $newPassword,
                'newAvatar' => $newAvatar,
                'newCover' => $newCover
            ];            

            if($name && $birthdate && $email){
                UserHandler::updateUser($this->loggedUser->id, $updateInputs);
            } else {
                $_SESSION['flash'] = 'É necessário a conta ter nome, e-mail e data de nascimento.';
                $this->redirect('/config');
            } 
            
        } else {
            $_SESSION['flash'] = 'Senha incorreta.';
            $this->redirect('/config');
        }

        $this->redirect('/config');
    }

    private function cutImage($file, $w, $h, $folder) {
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h) {
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x < 0 ? $x / 2 : $x;
        $y < 0 ? $y / 2 : $y;
        
        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']){
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng(($file['tmp_name']));
            break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig,
        );

        $fileName = md5(time().rand(0, 9999)).'.jpg';

        imagejpeg($finalImage, "$folder/$fileName");

        return $fileName;
    }
}