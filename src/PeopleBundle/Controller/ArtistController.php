<?php

namespace PeopleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PeopleBundle\Entity\Artist;
use PeopleBundle\Form\ArtistType;

/**
 * Artist controller.
 *
 */
class ArtistController extends Controller
{
    /**
     * Lists all Artist entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $artists = $em->getRepository('PeopleBundle:Artist')->findAll();

        return $this->render('artist/index.html.twig', array(
            'artists' => $artists,
        ));
    }

    /**
     * Creates a new Artist entity.
     *
     */
    public function newAction(Request $request)
    {
        $artist = new Artist();
        $form = $this->createForm('PeopleBundle\Form\ArtistType', $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($artist);
            $em->flush();

            return $this->redirectToRoute('artist_show', array('id' => $artist->getId()));
        }

        return $this->render('artist/new.html.twig', array(
            'artist' => $artist,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Artist entity.
     *
     */
    public function showAction(Artist $artist)
    {
        $deleteForm = $this->createDeleteForm($artist);

        return $this->render('artist/show.html.twig', array(
            'artist' => $artist,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Artist entity.
     *
     */
    public function editAction(Request $request, Artist $artist)
    {
        $deleteForm = $this->createDeleteForm($artist);
        $editForm = $this->createForm('PeopleBundle\Form\ArtistType', $artist);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($artist);
            $em->flush();

            return $this->redirectToRoute('artist_edit', array('id' => $artist->getId()));
        }

        return $this->render('artist/edit.html.twig', array(
            'artist' => $artist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Artist entity.
     *
     */
    public function deleteAction(Request $request, Artist $artist)
    {
        $form = $this->createDeleteForm($artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($artist);
            $em->flush();
        }

        return $this->redirectToRoute('artist_index');
    }

    /**
     * Creates a form to delete a Artist entity.
     *
     * @param Artist $artist The Artist entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Artist $artist)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('artist_delete', array('id' => $artist->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
