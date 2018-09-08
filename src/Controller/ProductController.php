<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
     * @Route("/", name="newProduct", methods={"POST"})
     */
    public function new(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent(), true);

        $product = new Product($content["name"], $content["description"], $content["price"], $content["base64Image"]);

        $manager->persist($product);
        $manager->flush();

        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
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
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
     * @Route("/{product}", name="updateProduct", methods={"PATCH"})
     */
    public function update(Product $product, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent(), true);

        foreach ($content as $key => $value) {
            $product->{'set'.ucfirst($key)}($value);
        }

        $manager->persist($product);
        $manager->flush();

        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
