<?php

namespace App\Controller;

use App\Services\JwtAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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



    public function create(Request $request,JwtAuth $jwtAuth){


       $data =[

        "Status" => "Error",
        "Code"=>500,
        "Message"=>"Video no Guardado"
       ];




       return $this->resJson($data);

    }
}



