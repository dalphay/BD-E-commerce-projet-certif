<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

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
}
