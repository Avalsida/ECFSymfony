<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
           return $this->redirectToRoute('app_home');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/subscriptions', name:'user_subscriptions')]
    public function subscription(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login');
        }
        $subscriptions = $user->getSubscriptions();
        return $this->render('profile/subscriptions.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
    #[Route('/profile/edit', name: 'user_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || in_array('ROLE_BANNED', $user->getRoles()))
        {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ProfileFormType::class, $user
        , ['attr' => ['novalidate' => 'novalidate']]
    );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('current_password')->getData();
            $newPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérifiez les champs requis pour s'assurer qu'ils ne sont pas null
            $user->setNom($form->get('nom')->getData() ?: '');
            $user->setPrenom($form->get('prenom')->getData() ?: '');
            $user->setEmail($form->get('email')->getData() ?: '');
            $user->setAdresse($form->get('adresse')->getData() ?: '');
            $user->setCodePostal($form->get('code_postal')->getData() ?: '');
            $user->setVille($form->get('ville')->getData() ?: '');
            $user->setNumeroTelephone($form->get('numero_telephone')->getData() ?: '');

            if ($newPassword || $confirmPassword) {
                if ($currentPassword && $passwordHasher->isPasswordValid($user, $currentPassword)) {
                    if ($newPassword === $confirmPassword) {
                        $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($encodedPassword);
                    } else {
                        $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                        return $this->redirectToRoute('user_profile_edit');
                    }
                } else {
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                    return $this->redirectToRoute('user_profile_edit');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
