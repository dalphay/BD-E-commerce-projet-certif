<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/user/login", name="login", methods={"POST"})
     */
    public function login()
    {
        $user = $this->getUser();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $normalizers[0]->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $this->serializer = new Serializer($normalizers, $encoders);

        $data = $this->serializer->normalize($user, null, ['attributes' => ['id', 'role', 'email']]);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/user/logout", name="logout", methods={"GET"})
     */
    public function logout(){
        throw new Exception("This should not be executed !");
    }
}
