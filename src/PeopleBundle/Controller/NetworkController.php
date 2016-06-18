<?php

namespace PeopleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PeopleBundle\Entity\Network;
use PeopleBundle\Form\NetworkType;

/**
 * Network controller.
 *
 */
class NetworkController extends Controller
{
    /**
     * Lists all Network entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $networks = $em->getRepository('PeopleBundle:Network')->findAll();

        return $this->render('network/index.html.twig', array(
            'networks' => $networks,
        ));
    }

    /**
     * Creates a new Network entity.
     *
     */
    public function newAction(Request $request)
    {
        $network = new Network();
        $form = $this->createForm('PeopleBundle\Form\NetworkType', $network);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($network);
            $em->flush();

            return $this->redirectToRoute('network_show', array('id' => $network->getId()));
        }

        return $this->render('network/new.html.twig', array(
            'network' => $network,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Network entity.
     *
     */
    public function showAction(Network $network)
    {
        $deleteForm = $this->createDeleteForm($network);

        return $this->render('network/show.html.twig', array(
            'network' => $network,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Network entity.
     *
     */
    public function editAction(Request $request, Network $network)
    {
        $deleteForm = $this->createDeleteForm($network);
        $editForm = $this->createForm('PeopleBundle\Form\NetworkType', $network);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($network);
            $em->flush();

            return $this->redirectToRoute('network_edit', array('id' => $network->getId()));
        }

        return $this->render('network/edit.html.twig', array(
            'network' => $network,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Network entity.
     *
     */
    public function deleteAction(Request $request, Network $network)
    {
        $form = $this->createDeleteForm($network);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($network);
            $em->flush();
        }

        return $this->redirectToRoute('network_index');
    }

    /**
     * Creates a form to delete a Network entity.
     *
     * @param Network $network The Network entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Network $network)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('network_delete', array('id' => $network->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
