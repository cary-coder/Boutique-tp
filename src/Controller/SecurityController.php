<?php

namespace App\Controller;

use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/passer-en-admin_{id<\d+>}", name="paser_en_admin")
     */

     public function passerEnAdmin(UserRepository $repo, Request $request, $id)
     {
        $secret = "123456";

        $form =$this->createForm(AdminType::class);
        $form->handleRequest($request);
    // on recupere l'user dont l'id est passé en url
        $user = $repo->find($id);

        if (!$user) 
        {
            $this->addFlash("error", "aucun utilisateur trouvé avec l'id $id");
            return $this->redirectToRoute("app_home");
        }

        if($form->isSubmitted() && $form->isValid() )
        {
        //recupere le secret dans AdmidType
        //et si correspond au mdp stocke dans la variable $authenticationUtilssecret
            if($form->get('secret')->getData()==$secret)
            {
                $user->setRoles(["ROLE_ADMIN"]);
            }else
            {
                $this->addFlash("error", "vous n'avez pas les droits pour affecter cette action");
                return $this->redirectToRoute("app_home");
            }

            //en passant par la methode add du Repository, l'objet sera persisté et envoyé
            //en bdd grâce au parametre 1 (true)
            $repo->add($user, 1);

            $this->addFlash("success", "vous êtes desormais Admin, veuillez vous reconnecter pour profiter de vos priviléges");
            return $this->redirectToRoute("app_home");
        }
            return $this->render("security/passerEnAdmin.html.twig", [
                "user"=>$user,
                "formAdmin"=>$form->createView()
            ]);
     }
}

