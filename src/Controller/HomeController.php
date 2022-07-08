<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $repo): Response
    {
        $derniersProduits= $repo->findBy([], ["dateEnregistrement"=>"DESC"], 8);
        
        //dd($derniersProduits);

        return $this->render('home/index.html.twig', [
            'produits' => $derniersProduits
        ]);
    }
}
