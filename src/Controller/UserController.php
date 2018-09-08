<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\ShoppingCart;

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
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user", name="oneUser", methods={"GET"})
     */
    public function one()
    {
        $user = $this->getUser();

        $json = $this->serializer->serialize($user, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/user", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);

        $user = new User(
            $content["name"],
            $content["surname"],
            $content["address"],
            $content["gender"],
            $content["email"]            
        );

        $encoded = $encoder->encodePassword($user, $content["password"]);
        $user->setPassword($encoded);

        $shoppingCart = new ShoppingCart();
        $user->setShoppingCart($shoppingCart);

        $manager->persist($user);
        $manager->flush();

        $json = $this->serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user", name="updateUser", methods={"PATCH"})
     */
    public function update(Request $request)
    {
        $user = $this->getUser();

        $manager = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);

        foreach ($content as $key => $value) {
            $user->{'set'.ucfirst($key)}($value);
        }

        if ($user->getPassword() == $content["password"]) {
            $encoded = $encoder->encodePassword($user, $content["password"]);
            $user->setPassword($encoded);
        }

        $manager->persist($user);
        $manager->flush();

        $json = $this->serializer->serialize($user, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
