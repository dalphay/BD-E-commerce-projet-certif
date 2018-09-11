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
    const NORMALIZER_FORMAT = ['attributes' => ['id', 'name', 'surname', 'gender', 'address', 'email']];

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

        // get current user based on session
        $user = $this->getUser();

        // https://symfony.com/doc/current/components/serializer.html#usage

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        // we make sure that encoder doesn't enter in an infinite loop by limiting recursive depth of instances
        // https://symfony.com/doc/current/components/serializer.html#handling-circular-references
        $normalizers[0]->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $this->serializer = new Serializer($normalizers, $encoders);

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($user, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
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
