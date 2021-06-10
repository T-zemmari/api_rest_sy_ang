<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Video;


class UserController extends AbstractController
{

    // creamos un metodo que solo le vamos a dar uso en el controlodaor 

    private function resJson($data) {

        //serializar datos con servicio serializer

        $json = $this->get('serializer')->serialize($data,'json');

        //Response con httpfoudation

        $response = new Response();

        // Asignar contenido a la respuesta

        $response->setContent($json);

        //Indicar formato de la respuesta
        
        $response->headers->set('content-Type','application/json');

        //Devolver la respuesta
        return $response;
        
    }
   
    public function index()
    {

       $user_repo = $this->getDoctrine()->getRepository(User::class);
       $video_repo = $this->getDoctrine()->getRepository(Video::class);

       $users = $user_repo->findAll();

       $data = [
        'message' => 'Welcome to your new controller!',
        'path' => 'src/Controller/UserController.php',
       ];

      /* foreach($users as $user){
           echo "<h1>{$user->getName()} {$user->getLastname()}</h1>";
       };
       foreach($user->getVideos() as $video){
        echo "<p>{$video->getUrl()}</p>";

       }*/
    
       //die();

        return $this->resJson($users);
    }
}
