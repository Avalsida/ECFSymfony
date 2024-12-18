<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{_locale}', name: 'change_language')]
    public function changeLanguage(Request $request, $_locale, TranslatorInterface $translator): Response
    {
        // Mettre à jour la locale dans la session
        $request->getSession()->set('_locale', $_locale);
        $request->setLocale($_locale);

        
        return $this->redirectToRoute('app_home', ['_locale' => $_locale]);
    }
}
