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

class ProductController extends Controller
{
    private $serializer;
    // the format of our json response
    const NORMALIZER_FORMAT = ['attributes' => ['id', 'name', 'description', 'price', 'imageURI']];

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
     * @Route("/product", name="allProducts", methods={"GET"})
     */
    public function all(ProductRepository $repo)
    {
        // get all products by calling findAll() on products repository
        $products = $repo->findAll();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($products, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/product/{product}", name="oneProduct", methods={"GET"})
     * by mentioning `{object}` inside a route, we tell to symfony to automatically instanciate an object from its id
     */
    public function one(Product $product)
    {
        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($products, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
     * @Route("/product", name="newProduct", methods={"POST"})
     * by mentioning `{object}` inside a route, we tell to symfony to automatically instanciate an object from its id
     */
    public function new(Request $request)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();

        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // creating a new product
        $product = new Product($content["name"], $content["description"], $content["price"], $content["base64Image"]);

        // telling to manager to persist our entity instance in database
        $manager->persist($product);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($product, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
     * @Route("/product/{product}", name="deleteProduct", methods={"DELETE"})
     */
    public function del(Product $product)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();

        // telling to manager to remove our entity instance from database
        $manager->remove($product);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($product, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="You must be a logged admin.")
     * @Route("/product/{product}", name="updateProduct", methods={"PATCH"})
     */
    public function update(Product $product, Request $request)
    {
        // the manager allow us to persist entity instance to database
        $manager = $this->getDoctrine()->getManager();

        // deserializing request json
        $content = json_decode($request->getContent(), true);

        // PATCH method allow us to send partial object for updating. So we must update properties
        // only when its required, to do that we call setter methods based on key names sended by
        // the request. For example : if request contains {"name": "toto"} this loop will call
        // $object->setName("toto")
        foreach ($content as $key => $value) {
            $product->{'set' . ucfirst($key)}($value);
        }

        // telling to manager to persist our entity instance in database
        $manager->persist($product);
        // executing SQL
        $manager->flush();

        // we can customize the data format returned by mapping it in an array (here NORMALIZER_FORMAT)
        // see https://symfony.com/doc/current/components/serializer.html#selecting-specific-attributes
        $data = $this->serializer->normalize($product, null, self::NORMALIZER_FORMAT);
        // convert formated datas to json using serialize()
        $json = $this->serializer->serialize($data, 'json');

        // prepare response object
        $response = new Response($json);
        // setup response headers
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
