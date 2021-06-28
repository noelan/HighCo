<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TransactionRepository $transactionRepository): Response
    {

        $data = $transactionRepository->getTotalAmountGroupedByPeople();
        $totalAmount =  array_column($data,'total_amount');
        $donators = array_column($data,'donators');

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        
        $totalAmountJson = $serializer->serialize($totalAmount, 'json');
        $donatorsJson = $serializer->serialize($donators, 'json');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'totalAmountJson' => $totalAmountJson,
            'donatorsJson' => $donatorsJson
        ]);
    }
}
