<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
}
