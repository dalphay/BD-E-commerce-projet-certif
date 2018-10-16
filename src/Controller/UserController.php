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
     * @Route("/user", name="register", methods={"GET"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // creating new Us/userer
        $user = new User(
            "alpha@yahoo.fr"
        );

        // encode and save password
        $encoded = $encoder->encodePassword($user, "1234");
        $user->setAddress("123 rue alexandre");
        $user->setGender(1);
        $user->setSurname("diallo");
        $user->setName('alpha');
        $user->setEmail('korka@gmail.com');
        $user->setPassword($encoded);
         // create the associated shopping cart
         $test = new ShoppingCart();
         $user->setShoppingCart($test);
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
