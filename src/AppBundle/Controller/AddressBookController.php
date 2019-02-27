<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AddressBook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Addressbook controller.
 *
 * @Route("/")
 */
class AddressBookController extends Controller
{
    /**
     * Lists all addressBook entities.
     *
     * @Route("/", name="addressbook_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@View/addressbook/index.html.twig');
    }

    /**
     * Lists all addressBook entities.
     *
     * @Route("/list/", name="addressbook_list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:AddressBook')->getAddressBooksList($request->get('filter_key'),$request->get('filter_value'));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, $request->query->getInt('page', 1), $this->getParameter('page_range')
        );
        return $this->render('@View/addressbook/list.html.twig', array(
            'pagination' => $pagination,
        ));
    }


    /**
     * Creates a new addressBook entity.
     *
     * @Route("/new", name="addressbook_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $addressBook = new Addressbook();
        $form = $this->createForm('AppBundle\Form\AddressBookType', $addressBook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $addressBook->getPicture();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('picture_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                }
                $addressBook->setPicture($fileName);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($addressBook);
            $em->flush();
            $this->addFlash("success", "Address Book added success");
            return $this->redirectToRoute('addressbook_index', array('id' => $addressBook->getId()));
        }

        return $this->render('@View/addressbook/new.html.twig', array(
            'addressBook' => $addressBook,
            'form'        => $form->createView(),
        ));
    }

    /**
     * Finds and displays a addressBook entity.
     *
     * @Route("/{id}", name="addressbook_show")
     * @Method("GET")
     */
    public function showAction(AddressBook $addressBook)
    {
        return $this->render('@View/addressbook/show.html.twig', array(
            'addressBook' => $addressBook,
        ));
    }

    /**
     * Displays a form to edit an existing addressBook entity.
     *
     * @Route("/{id}/edit", name="addressbook_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AddressBook $addressBook)
    {
        $editForm = $this->createForm('AppBundle\Form\AddressBookType', $addressBook);
        $editForm->handleRequest($request);
        $imgDir = $this->getParameter('picture_directory');
        $oldPicture = $addressBook->getPicture();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $file = $addressBook->getPicture();
            if ($file) {
                if($oldPicture){
                    //remove old
                    if(is_file("$imgDir/$oldPicture") && file_exists("$imgDir/$oldPicture")){
                        unlink("$imgDir/$oldPicture");
                    }
                }
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('picture_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                }
                $addressBook->setPicture($fileName);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "Address Book updated success");

            return $this->redirectToRoute('addressbook_edit', array('id' => $addressBook->getId()));
        }

        return $this->render('@View/addressbook/edit.html.twig', array(
            'addressBook' => $addressBook,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a addressBook entity.
     *
     * @Route("/{id}", name="addressbook_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AddressBook $addressBook)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($addressBook);
        $em->flush();
        $this->addFlash("success", "Address Book deleted success");

        return new Response('');
    }

}
