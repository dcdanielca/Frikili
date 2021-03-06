<?php

namespace App\Controller;

use App\Entity\UserFriki;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController extends AbstractController
{
    /**
     * @Route("/signup", name="sign_up")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new UserFriki();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user, $form['password']->getData()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('exito', UserFriki::SUCCESS_SIGNUP);
            return $this->redirectToRoute('sign_up');
        }
        return $this->render('sign_up/index.html.twig', [
            'controller_name' => 'SignUpController',
            'form' => $form -> createView()
        ]);
    }
}
