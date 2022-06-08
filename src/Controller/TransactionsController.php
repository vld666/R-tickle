<?php

namespace App\Controller;

use App\Entity\Transactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/transactions", name="app_transactions")
     */
    public function transactions(): Response
    {
        $transactions = $this->em->getRepository(Transactions::class)->findAll();

        return $this->render('/transactions/index.html.twig',[
            'transactions' => $transactions
        ]);
    }

    /**
     * @Route("/api/transactions/show/{id}", methods={"GET"})
     */
    public function apiTransactionsIndex(Transactions $transaction): Response
    {
        $transaction = $this->em->getRepository(Transactions::class)->showTransaction($transaction);

        $response = new JsonResponse($transaction);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $this->respond($transaction);
    }

    /**
     * @Route("/api/transactions/index", methods={"GET"})
     */
    public function apiShowCategories(): Response
    {
        $transactions = $this->em->getRepository(Transactions::class)->findAll();
        $arrayCollection = array();

        foreach($transactions as $transaction){
            $arrayCollection[] = array(
                'id' => $transaction->getId(),
                'wallet' => $transaction->getWalletId(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'createdAt' => $transaction->getCreatedAt()
            );
        }

        $response = new JsonResponse($arrayCollection);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $response;
    }
}