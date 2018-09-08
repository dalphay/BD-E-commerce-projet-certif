<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/user/login", name="login", methods={"POST"})
     * https://symfony.com/doc/current/security/json_login_setup.html
     */
    public function login()
    {
        /**
         * Don't let this controller confuse you.
         * When you submit a POST request to the /login URL with the following JSON document as the body, the security system intercepts the requests.
         * It takes care of authenticating the user with the submitted username and password or triggers an error in case the authentication process fails
         * 
         * {"username": "toto", "password": "S0l!dP455w0rd"}
         */

        $user = $this->getUser();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $normalizers[0]->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer($normalizers, $encoders);

        $json = $this->serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * @Route("/user/logout", name="logout", methods={"GET"})
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
