<?php

namespace FetchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FetchBundle\Entity\Source;
use FetchBundle\Form\SourceType;

/**
 * Source controller.
 *
 */
class SourceController extends Controller
{
    /**
     * Lists all Source entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sources = $em->getRepository('FetchBundle:Source')->findAll();

        return $this->render('source/index.html.twig', array(
            'sources' => $sources,
        ));
    }

    /**
     * Creates a new Source entity.
     *
     */
    public function newAction(Request $request)
    {
        $source = new Source();
        $form = $this->createForm('FetchBundle\Form\SourceType', $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();

            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return $this->render('source/new.html.twig', array(
            'source' => $source,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Source entity.
     *
     */
    public function showAction(Source $source)
    {
        $deleteForm = $this->createDeleteForm($source);

        return $this->render('source/show.html.twig', array(
            'source' => $source,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Source entity.
     *
     */
    public function editAction(Request $request, Source $source)
    {
        $deleteForm = $this->createDeleteForm($source);
        $editForm = $this->createForm('FetchBundle\Form\SourceType', $source);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();

            return $this->redirectToRoute('source_edit', array('id' => $source->getId()));
        }

        return $this->render('source/edit.html.twig', array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Source entity.
     *
     */
    public function deleteAction(Request $request, Source $source)
    {
        $form = $this->createDeleteForm($source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($source);
            $em->flush();
        }

        return $this->redirectToRoute('source_index');
    }

    /**
     * Creates a form to delete a Source entity.
     *
     * @param Source $source The Source entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Source $source)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('source_delete', array('id' => $source->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
