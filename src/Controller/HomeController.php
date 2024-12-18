<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    // #[Route('/', name: 'default_redirect')] 
    // public function redirectToLocale(Request $request): RedirectResponse 
    // { 
    //     $locale = $request->getPreferredLanguage(['en', 'fr']);
    //      // Détermine la langue préférée de l'utilisateur 
    //      return new RedirectResponse('/' . $locale . '/'); }
    
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $mavariable = "Hello World";
        return $this->render('home/index.html.twig', [
            'mavar' => $mavariable,
        ]);
    }
}
