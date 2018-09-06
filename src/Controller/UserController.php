<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    private $serializer;

    public function __construct()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $normalizers[0]->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/user/{user}", name="oneUser", methods={"GET"})
     */
    public function one(User $user)
    {
        $json = $this->serializer->serialize($user, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/user", name="addUser", methods={"POST"})
     */
    public function new(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($content["name"]);
        $user->setSurname($content["surname"]);
        $user->setGender($content["gender"]);
        $user->setEmail($content["email"]);
        $user->setAddress($content["address"]);

        $shoppingCart = new ShoppingCart();
        $user->setShoppingCart($shoppingCart);

        $manager->persist($user);
        $manager->flush();

        $json = $this->serializer->serialize($user, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/user/{user}", name="updateUser", methods={"PUT"})
     */
    public function update(Request $request, User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);

        $user->setName($content["name"]);
        $user->setSurname($content["surname"]);
        $user->setGender($content["gender"]);
        $user->setEmail($content["email"]);
        $user->setAddress($content["address"]);

        $manager->persist($user);
        $manager->flush();

        $json = $this->serializer->serialize($user, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
