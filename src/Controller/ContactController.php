<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactAddType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
    #[Route('/contact/details/{id}', name: 'contact_details')]
    public function contact_details($id, ManagerRegistry $doctrine): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        return $this->render('contact/details.html.twig', [
            'contact' => $contact,
        ]);
    }


    #[Route('/contact/delete/{id}', name: 'contact_delete')]
    public function contact_delete($id, ManagerRegistry $doctrine)
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($contact);
        $entityManager->flush();
        $this->addFlash('success','Le contact a été supprimé');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/contact/edit/{id}', name: 'contact_edit')]
    public function contact_edit($id, ManagerRegistry $doctrine, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        $form = $this->createForm(ContactAddType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success','Le contact a été modifié');
            return $this->redirectToRoute('app_home');
        }
        
        return $this->renderForm('contact/editContact.html.twig', [
            'formContact' => $form,
        ]);
    }

    #[Route('/contact/add/', name: 'contact_add')]
    public function contact_add(ManagerRegistry $doctrine, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();
        $contact = new Contact();
        $form = $this->createForm(ContactAddType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success','Le contact a été ajouté');
            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('contact/addContact.html.twig', [
            'formContact' => $form,
        ]);
    }


}
