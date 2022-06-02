<?php

namespace App\Controller;

use App\Entity\PaidArticles;
use App\Entity\Transactions;
use App\Entity\UserWallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/admin/stats/sales", methods={"GET", "POST"}, name="app_stats_sales")
     */
    public function sales(): Response
    {
        $totalSales = count($this->em->getRepository(PaidArticles::class)->findAll());
        $totalFees  = $this->em->getRepository(Transactions::class)->getTotalSales();

//        dd($totalFees2);

        return $this->render('/stats/sales.html.twig',[
            'totalSales' => $totalSales,
            'totalFees' => $totalFees,
        ]);
    }



}