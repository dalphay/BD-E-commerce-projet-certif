<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\ShoppingCart;

class MainController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'formValues' => [],
        ]);
    }

    /**
     * @Route("/submit", name="submit", methods={"POST"})
     */
    public function submit(Request $request)
    {
        $formValues["name"] = $request->request->get("name");
        $formValues["surname"] = $request->request->get("surname");
        $formValues["gender"] = $request->request->get("gender");

        return $this->render('main/index.html.twig', [
            'formValues' => $formValues,
        ]);
    }

    /**
     * @Route("/donotuse", name="submit", methods={"GET"})
     */
    public function add(Request $request)
    {

        $manager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product->setName("Ordinateur");
        $product->setDescription("Your life is over.");
        $product->setPrice(100000);

        dump($product);
        $manager->persist($product);
        $manager->flush();
        dump($product);

        return $this->render('main/index.html.twig', [
            'formValues' => [],
        ]);
    }

    /**
     * @Route("/donotuser", name="submit", methods={"GET"})
     */
    public function toto(Request $request)
    {

        // User & ShoppingCart
        // ToBuy
        $manager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName("Colomb");
        $user->setSurname("Christophe");
        $user->setGender(1);
        $user->setEmail("chris@colomb.us");
        $user->setAddress("123 rue bidon 12345 New York");
        
        $shoppingCart = new ShoppingCart();
        $user->setShoppingCart($shoppingCart);

        $manager->persist($user);
        $manager->flush();

        return $this->render('main/index.html.twig', [
            'formValues' => [],
        ]);
    }
}
