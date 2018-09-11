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
    // the format of our json response
    const NORMALIZER_FORMAT = ['attributes' => ['id', 'name', 'surname', 'gender', 'address', 'email']];

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
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user", name="oneUser", methods={"GET"})
     */
    public function one()
    {
        // get current user based on session
        $user = $this->getUser();

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
     * @Route("/user", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // creating new User
        $user = new User(
            $content["name"],
            $content["surname"],
            $content["address"],
            $content["gender"],
            $content["email"]
        );

        // encode and save password
        $encoded = $encoder->encodePassword($user, $content["password"]);
        $user->setPassword($encoded);

        // create the associated shopping cart
        $shoppingCart = new ShoppingCart();
        $user->setShoppingCart($shoppingCart);

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

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user", name="updateUser", methods={"PATCH"})
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder)
    {
        // get current user based on session
        $user = $this->getUser();

        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // PATCH method allow us to send partial object for updating. So we must update properties
        // only when its required, to do that we call setter methods based on key names sended by
        // the request. For example : if request contains {"name": "toto"} this loop will call
        // $object->setName("toto")
        foreach ($content as $key => $value) {
            // if password have been sent, we encode it
            if ($key == "password") {
                $value = $encoder->encodePassword($user, $content["password"]);
            }
            $user->{'set' . ucfirst($key)}($value);
        }

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
