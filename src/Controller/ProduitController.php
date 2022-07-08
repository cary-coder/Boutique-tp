<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
/**
 * @Route("/produit/{id<\d+>}", name= "produit_show" )
 */
    

public function show($id, ProduitRepository $repo)
{
      $produit =$repo-> find($id);

      return $this->render("produit/show.html.twig", [
            'produit'=> $produit
      ]);
}


/**
 * @Route("/produits", name="produits_all")
 */

 public function all(ProduitRepository $repoPro, CategorieRepository $repoCat){

      $produits = $repoPro->findAll();
      $categories = $repoCat->findAll();

          return $this->render("produit/all.html.twig", [
            'produits'=> $produits,
            'categories' => $categories
      ]);
 }

/**
 * @Route("/categorie-{id<\d+>}", name="produits_categorie")
 */
 public function categorieProduits($id, CategorieRepository $repo)
 {
       // on recupere la categorie sur laquelle on a cliqué
       $categorie =$repo->find($id);

       $categories = $repo->findAll();

       //on recupere la liste de toutes les categories
       return $this->render('produit/all.html.twig', [
            'produits' =>  $categorie->getProduits(),
            'categories' => $categories
       ]);
 }

}
