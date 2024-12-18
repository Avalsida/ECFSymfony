<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SubscriptionController extends AbstractController
{
    #[Route('/subscribe', name: 'app_subscribe')]
    public function subscribe(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $subscription = $user->getActiveSubscription();
        $isExistingSubscription = $subscription !== null;

        if (!$isExistingSubscription) {
            $subscription = new Subscription();
            $subscription->setUser($user);
            $subscription->setDateDebut(new \DateTime());
        }

        $form = $this->createForm(SubscriptionFormType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $prix = $type === 'monthly' ? 23.99 : (12 * 23.99 * 0.9);

            $subscription->setType($type);
            $subscription->setPrix($prix);

            if ($isExistingSubscription) {
                $currentEndDate = $subscription->getDateFin();
                if ($type === 'monthly') {
                    $subscription->setDateFin($currentEndDate->modify('+1 month'));
                } else {
                    $subscription->setDateFin($currentEndDate->modify('+1 year'));
                }
            } else {
                if ($type === 'monthly') {
                    $subscription->setDateFin((new \DateTime())->modify('+1 month'));
                } else {
                    $subscription->setDateFin((new \DateTime())->modify('+1 year'));
                }
            }

            $entityManager->persist($subscription);
            $entityManager->flush();

            return $this->redirectToRoute('user_subscriptions');
        }

        $message = $request->query->get('message', '');

        return $this->render('subscription/subscribe.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }
}
