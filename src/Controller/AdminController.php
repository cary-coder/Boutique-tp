<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\CategorieType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/admin", name="admin_")
*/

class AdminController extends AbstractController
{
    /**
    * @Route("/ajout-produit", name="ajout_produit")
    */
    public function ajout(Request $request, EntityManagerInterface $manager,  SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            
            $imageFile=$form->get('photoForm')->getData();
            
            //on cree un nouveu nom pour la nouvelle image
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $imageFile->guessExtension();
            
            //on deplace l'image dans le dossier parametré dans service.yaml
            try{
                $imageFile->move($this->getParameter('photos_produits'), $fileName);
            }catch(FileException $e){
                //gestion des erreurs upload image
            }
            $produit->setPhoto($fileName);
            //on récupère le manager de doctrine
            
            $produit->setDateEnregistrement(new DateTime("now"));
            // $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($produit);
            $manager->flush();
            
        }
        
        return $this->render('admin/formulaire.html.twig', [
            'formProduit' => $form->createView()
        ]);
    }
    
    
    /**
    * @Route("/gestion-produits", name="gestion_produits")
    */
    public function gestionProduits(ProduitRepository $repo)
    {
        $produits = $repo->findAll();
        
        return $this->render("admin/gestion-produits.html.twig", [
            'produits' => $produits
        ]);
        
    }
    
    /**
    *@Route("/details-produit-{id<\d+>}", name="details_produit")
    */
    public function detailsProduit($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);
        
        
        return $this->render("admin/details-produit.html.twig", [
            'produit' => $produit
        ]);
    }
    
    /**
    * @Route("/update-produit-{id<\d+>}", name="update_produit")
    */
    public function update($id, ProduitRepository $repo, Request $request,  SluggerInterface $slugger)
    {
        $produit = $repo->find($id);
        
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid()) 
        {
            
            if($form->get('photoForm')->getData())
            {
                    $imageFile=$form->get('photoForm')->getData();
                    
                    //on cree un nouveu nom pour la nouvelle image
                    $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $imageFile->guessExtension();
                    
                    //on deplace l'image dans le dossier parametré dans service.yaml
                    try{
                        $imageFile->move($this->getParameter('photos_produits'), $fileName);
                    }catch(FileException $e){
                        //gestion des erreurs upload image
                    }
                    $produit->setPhoto($fileName);
                    //on récupère le manager de doctrine
            }
            
                $repo->add($produit, 1);
                
                return $this->redirectToRoute("admin_gestion_produits");
        }
        
        return $this->render("admin/formulaire.html.twig", [
            'formProduit' => $form->createView()
        ]);
    }
    

    /**
    * @Route("/delete-produit-{id<\d+>}", name="delete_produit")
    */
    public function delete($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);
        
        $repo->remove($produit, 1);
        
        return $this->redirectToRoute("admin_gestion_produits");
    }

    
    /**
    *  @Route("/categorie-ajout", name="ajout_categorie")
    */
    public function ajoutCategorie(Request $request, CategorieRepository $repo)
    { 
        $categorie = new Categorie();
        
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $repo->add($categorie, 1);
            
            return $this->redirectToRoute("app_home");
        }
        
        return $this->render("admin/formCategorie.html.twig", [
            "formCategorie" => $form->createView()
        ]);
    }
    
}
