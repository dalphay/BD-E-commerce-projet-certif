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
     * @Route("/user/shopping_cart", name="getShoppingCart", methods={"GET"})
     */
    public function one()
    {
        $user = $this->getUser();
        $shoppingCart = $user->getShoppingCart();

        $json = $this->serializer->serialize($shoppingCart, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user/shopping_cart/product/{product}", name="addToShoppingCart", methods={"POST"})
     */
    public function add(Product $product, Request $request)
    {
        $user = $this->getUser();

        $manager = $this->getDoctrine()->getManager();
        $shoppingCart = $user->getShoppingCart();
        $content = json_decode($request->getContent(), true);

        $shoppingCart->addOrIncrementToBuy($product, $content["qty"]);
        $shoppingCart->computeTotal();

        $manager->persist($shoppingCart);
        $manager->flush();

        $json = $this->serializer->serialize($shoppingCart, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=403, message="You must be logged.")
     * @Route("/user/shopping_cart/product/{product}", name="removeFromShoppingCart", methods={"DELETE"})
     */
    public function del(Product $product)
    {
        $user = $this->getUser();

        $manager = $this->getDoctrine()->getManager();
        $shoppingCart = $user->getShoppingCart();

        foreach ($shoppingCart->getToBuys() as $value) {
            if ($value->getProduct()->getId() == $product->getId()) {
                $manager->remove($value);
            }
        }

        $shoppingCart->computeTotal();
        
        $manager->persist($shoppingCart);
        $manager->flush();

        $json = $this->serializer->serialize($shoppingCart, "json");

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
