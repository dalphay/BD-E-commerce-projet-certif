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
            'formValue' => '',
        ]);
    }

    /**
     * @Route("/submit", name="submit", methods={"POST"})
     */
    public function submit(Request $request)
    {
        $name = $request->request->get("name");
        return $this->render('main/index.html.twig', [
            'formValue' => $name,
        ]);
    }
}
