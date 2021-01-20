<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\PaymentDetails;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\PaymentDetailsType;
use App\Form\PersonalDetailsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="app_registration")
     */
    public function register(): Response
    {
        /** @var User $user */
        $user = $this->session->get('registeringUser');
        $step = $this->getCurrentStep($user);

        return $this->redirectToStep($step);
    }

    /**
     * @Route("/step1", name="app_registration_step1")
     */
    public function step1(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(PersonalDetailsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->session->set('registeringUser', $user);

            return $this->redirectToRoute('app_registration_step2');
        }

        return $this->render('registration/step.html.twig', [
            'registrationForm' => $form->createView(),
            'step' => 1
        ]);
    }

    /**
     * @Route("/step2", name="app_registration_step2")
     */
    public function step2(Request $request): Response
    {
        /** @var User $user */
        $user = $this->session->get('registeringUser');

        $this->redirectToPreviousStepIfIncompleteData($user, 2);

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAddress($address);
            $this->session->set('registeringUser', $user);

            return $this->redirectToRoute('app_registration_step3');
        }

        return $this->render('registration/step.html.twig', [
            'registrationForm' => $form->createView(),
            'step' => 2
        ]);
    }

    /**
     * @Route("/step3", name="app_registration_step3")
     */
    public function step3(Request $request): Response
    {
        /** @var User $user */
        $user = $this->session->get('registeringUser');

        $this->redirectToPreviousStepIfIncompleteData($user, 3);

        $paymentDetails = new PaymentDetails();
        $form = $this->createForm(PaymentDetailsType::class, $paymentDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPaymentDetails($paymentDetails);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_registration_step4');
        }

        return $this->render('registration/step.html.twig', [
            'registrationForm' => $form->createView(),
            'step' => 3
        ]);
    }

    /**
     * @Route("/success", name="app_registration_step4")
     */
    public function success(): Response
    {
        $this->session->remove('registeringUser');

        return $this->render('registration/success.html.twig');
    }

    private function redirectToPreviousStepIfIncompleteData(?User $user, int $step)
    {
        $currentStep = $this->getCurrentStep($user);

        if ($currentStep < $step) {
            $this->redirectToStep($currentStep);
        }
    }

    private function getCurrentStep(?User $user): int
    {
        if (!$user) {
            return 1;
        }

        if (!$user->getAddress()) {
            return 2;
        }

        if (!$user->getPaymentDetails()) {
            return 3;
        }

        return 4;
    }

    private function redirectToStep(int $step): Response
    {
        return $this->redirectToRoute('app_registration_step' . $step);
    }
}
