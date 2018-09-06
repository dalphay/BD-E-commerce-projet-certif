<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/product")
 */
class ProductController extends Controller
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
     * @Route("/", name="allProducts", methods={"GET"})
     */
    public function all(ProductRepository $repo)
    {
        $products = $repo->findAll();
        $json = $this->serializer->serialize($products, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{product}", name="oneProduct", methods={"GET"})
     */
    public function one(Product $product)
    {
        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/", name="newProduct", methods={"POST"})
     */
    public function new(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent(), true);

        $product = new Product($content["name"], $content["description"], $content["price"]);

        $manager->persist($product);
        $manager->flush();

        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{product}", name="deleteProduct", methods={"DELETE"})
     */
    public function del(Product $product)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($product);
        $manager->flush();
        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{product}", name="updateProduct", methods={"PUT"})
     */
    public function update(Product $product, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent(), true);

        $product->setName($content["name"]);
        $product->setDescription($content["description"]);
        $product->setPrice($content["price"]);

        $manager->persist($product);
        $manager->flush();

        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
