<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;
use App\Services\JwtAuth;



class VideoController extends AbstractController
{

    private function resJson($data)
    {

        //serializar datos con servicio serializer

        $json = $this->get('serializer')->serialize($data, 'json');

        //Response con httpfoudation

        $response = new Response();

        // Asignar contenido a la respuesta

        $response->setContent($json);

        //Indicar formato de la respuesta

        $response->headers->set('content-Type', 'application/json');

        //Devolver la respuesta
        return $response;
    }

    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller video!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }



    public function create(Request $request, JwtAuth $jwtAuth)
    {


        $token = $request->headers->get('Autorization', null);


        $auth = $jwtAuth->authToken($token);

        $data = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "Video no Guardado"
        ];


        if ($auth) {

            $json = $request->get('json', null);
            $params = json_decode($json);

            $em = $this->getDoctrine()->getManager();
            $identity = $jwtAuth->authToken($token, true);

            


            if (!empty($json)) {


                $user = (!empty($identity->sub)) ? $identity->sub : null;
                $title = (!empty($params->title)) ? $params->title : null;
                $url = (!empty($params->url)) ? $params->url : null;
                $descripction = (!empty($params->descripction)) ? $params->descripction : null;
                $status = (!empty($params->status)) ? $params->status : null;


                if (!empty($title) && !empty($url)) {


                    $data = [

                        "Status" => "Success",
                        "code" => "200",
                        "message" => "Validacion correcta",

                    ];
                } else {
                    $data = [

                        "Status" => "Error",
                        "code" => "500",
                        "message" => "Validacion Incorrecta",

                    ];
                }

                $user_repo = $this->getDoctrine()->getRepository(User::class);
                $user = $user_repo->findOneBy([
                    "id"=>$identity->sub
                ]);

                $video = new Video();

                $video->setTitle($title);
                $video->setUrl($url);
                $video->setUser($user);
                $video->setStatus($status);
                $video->setDescription($descripction);

                $createdAt = new \DateTime('now');
                $updatedAt = new \DateTime('now');

                $video->setCreatedAt($createdAt);
                $video->setUpdatedAt($updatedAt);

                $video_repo = $this->getDoctrine()->getRepository(Video::class);


                $isset_video = $video_repo->findBy(array(
                    'url' => $url
                ));


                if (count($isset_video) == 0) {
                    $data = [
        
                        "Status" => "Success",
                        "code" => "200",
                        "message" => "Video Creado Correctamente",
                        "user" => $video
        
                    ];
                } else {
                    $data = [
        
                        "Status" => "Error",
                        "code" => "500",
                        "message" => "El Video ya existe"
        
                    ];
                }
        


                $em->persist($video);
                $em->flush();


            }
        }


       




        return $this->resJson($data);
    }


    // Listar los videos del usuario 

      public function listarVideos(){

        $data = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "No se encontraron videos"
        ];




        return $this->resJson($data);
      }
}
