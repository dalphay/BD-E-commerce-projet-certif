<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    private $serializer;
    // the format of our json response
    const NORMALIZER_FORMAT = ['attributes' => ['id', 'email', 'role']];

    public function __construct()
    {
        // https://symfony.com/doc/current/components/serializer.html#usage

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        // we make sure that encoder doesn't enter in an infinite loop by limiting recursive depth of instances
        // https://symfony.com/doc/current/components/serializer.html#handling-circular-references
        $normalizers[0]->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $this->serializer = new Serializer($normalizers, $encoders);
    }
    /**
     * @Route("/user", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // creating new Us/userer
        $user = new User(
            $content["email"]
        );

        // encode and save password
        $encoded = $encoder->encodePassword($user, $content["password"]);
        $user->setPassword($encoded);

        // telling to manager to persist our entity instance in database
        $manager->persist($user);
        // executing SQL
        $manager->flush();

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
}
