<?php

namespace App\Controller;

use App\Entity\Transactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends AbstractController
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
}