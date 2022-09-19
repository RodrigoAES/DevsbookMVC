<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class AjaxController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if($this->loggedUser === false){
            header('content-type: application/json');
            echo json_encode(['error' => 'Usuário não logado']);
            exit;
        }
    }

    public function like($atts) {
        $id = $atts['id'];

        if(PostHandler::isLiked($id, $this->loggedUser->id)) {
            PostHandler::deleteLike($id, $this->loggedUser->id);
        } else {
            PostHandler::addLike($id, $this->loggedUser->id);
        }
    }

    public function comment() {
        $array = ['error' => ''];

        $idPost = filter_input(INPUT_POST, 'id');
        $commentTxt = filter_input(INPUT_POST, 'txt');

        if($idPost && $commentTxt) {
            PostHandler::addComment($idPost, $commentTxt, $this->loggedUser->id);

            $array['link'] = '/perfil/'.$this->loggedUser->id;
            $array['avatar'] = '/media/avatars/'.$this->loggedUser->avatar;
            $array['name'] = $this->loggedUser->name;
            $array['body'] = $commentTxt;
        } else {
            $array = ['error' => 'ID da postagem e/ou comentário não enviados'];
        }

        header('content-type: application/json');
        echo json_encode($array);
        exit;
    }
    
    public function upload(){
        $array = ['error' => ''];

        if(isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])) {
            $photo = $_FILES['photo'];

            $maxWidth = 800;
            $maxHeight = 800;

            if(in_array($photo['type'], ['image/jpeg', 'image/png'])) {
                list($widthOrig, $heightOrig) = getimagesize($photo['tmp_name']);
                $ratio = $widthOrig / $heightOrig;
                
                $newWidth = $maxWidth;
                $newHeight = $maxHeight;
                $maxRatio = $maxWidth / $maxHeight;

                if($maxRatio > $ratio) {
                    $newWidth = $newHeight * $ratio;
                } else {
                    $newHeight = $newWidth / $ratio;
                }

                $finalImage = imagecreatetruecolor($newWidth, $newHeight);
                switch($photo['type']) {
                    case 'image/png':
                        $image = imagecreatefrompng($photo['tmp_name']);
                    break;
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($photo['tmp_name']);
                    break;
                }

                imagecopyresampled(
                    $finalImage, $image,
                    0, 0, 0, 0,
                    $newWidth, $newHeight, $widthOrig, $heightOrig
                );

                $photoName = md5(time().rand(0, 9999)).'.jpg';
                imagejpeg($finalImage, "media/uploads/".$photoName);

                PostHandler::addPost(
                    $this->loggedUser->id,
                    'photo',
                    $photoName
                );
            }

         
        } else {
            $array['error'] = 'Nenhuma imagem enviada';
        }

        header('content-type: application/json');
        echo json_encode($array);
        exit;        
    }
}