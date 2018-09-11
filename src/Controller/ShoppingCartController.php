<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Product;
use App\Entity\ToBuy;
use Symfony\Component\HttpFoundation\Request;

class ShoppingCartController extends Controller
{
    private $serializer;
    // the format of our json response
    const NORMALIZER_FORMAT = ['attributes' => ['id', 'total', 'toBuys' => ['qty', 'product' => ['id', 'name', 'description', 'price', 'imageURI']]]];

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
     * @Route("/user/shopping_cart", name="getShoppingCart", methods={"GET"})
     */
    public function one()
    {
        // get current user based on session
        $user = $this->getUser();
        $shoppingCart = $user->getShoppingCart();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($shoppingCart, null, self::NORMALIZER_FORMAT);
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
     * @Route("/user/shopping_cart/product/{product}", name="addToShoppingCart", methods={"POST"})
     */
    public function add(Product $product, Request $request)
    {
        // get current user based on session
        $user = $this->getUser();

        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        $shoppingCart = $user->getShoppingCart();

        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // add product to shopping cart
        $shoppingCart->addOrIncrementToBuy($product, $content["qty"]);
        // recompute shopping cart total price
        $shoppingCart->computeTotal();

        // telling to manager to persist our entity instance in database
        $manager->persist($shoppingCart);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($shoppingCart, null, self::NORMALIZER_FORMAT);
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
     * @Route("/user/shopping_cart/product/{product}", name="updateQty", methods={"PUT"})
     */
    public function updateQty(Product $product, Request $request)
    {
        // get current user based on session
        $user = $this->getUser();

        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        $shoppingCart = $user->getShoppingCart();
        
        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // searching the toBuy related to the product
        foreach ($shoppingCart->getToBuys() as $value) {
            if ($value->getProduct()->getId() == $product->getId()) {
                // when we find it, we updating the toBuy quantity
                $value->setQty($content["qty"]);
            }
        }
        // recompute shopping cart total price
        $shoppingCart->computeTotal();

        // telling to manager to persist our entity instance in database
        $manager->persist($shoppingCart);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($shoppingCart, null, self::NORMALIZER_FORMAT);
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
     * @Route("/user/shopping_cart/product/{product}", name="removeFromShoppingCart", methods={"DELETE"})
     */
    public function del(Product $product)
    {
        // get current user based on session
        $user = $this->getUser();

        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();
        $shoppingCart = $user->getShoppingCart();

        // searching the toBuy related to the product
        foreach ($shoppingCart->getToBuys() as $value) {
            if ($value->getProduct()->getId() == $product->getId()) {
                // when we find it, we remove the toBuy
                $shoppingCart->removeToBuy($value);
            }
        }

        // recompute shopping cart total price
        $shoppingCart->computeTotal();

        // telling to manager to persist our entity instance in database
        $manager->persist($shoppingCart);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($shoppingCart, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
