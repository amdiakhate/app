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
    public function showAction(Artist $artist, Request $request)
    {
        $deleteForm = $this->createDeleteForm($artist);
        $networks = $artist->getNetworks();
        //Tweets
        foreach ($networks as $network) {
            if ($network->getType() == 'twitter') {
                $twitter = $network->getId();
            }
        }
        $tweetsRep = $this->getDoctrine()->getRepository('TweetsBundle:Tweet');
        $tweets = $tweetsRep
            ->getWithUsersAndMedias(null, $network);

        $variables = $this->getVariables($request, $tweets, null);

        $response = $this->render(
            'TweetsBundle:Default:index.html.twig',
            array(
                'tweets' => $tweets,
                'vars' => $variables,
            )
        );

        return $response;
//        return $this->render('artist/show.html.twig', array(
//            'artist' => $artist,
//            'delete_form' => $deleteForm->createView(),
//        ));
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
            ->getForm();
    }

    /**
     * @param Request $request
     * @param Tweets[] $tweets
     * @param integer $firstTweetId
     *
     * @return array $vars
     */
    private function getVariables(Request $request, $tweets, $firstTweetId)
    {
        $vars = array(
            'first' => $firstTweetId,
            'previous' => null,
            'next' => null,
            'number' => 0,
            'cookieId' => $this->getLastTweetIdFromCookie($request),
            # No cookie by default
            'cookie' => null,
        );

        if (count($tweets) > 0) {
            $vars = $this->getTweetsVars($tweets, $vars);
        }

        return ($vars);
    }

    /**
     * @param Request $request
     * @return integer|null
     */
    private function getLastTweetIdFromCookie(Request $request)
    {
        if ($request->cookies->has('lastTweetId')) {
            return ($request->cookies->get('lastTweetId'));
        }
        // else
        return (null);
    }

    /**
     * If a Tweet is displayed, fetch data from repository
     *
     * @param Tweets[] $tweets
     * @param array $vars
     *
     * @return array $vars
     */
    private function getTweetsVars($tweets, $vars)
    {
        $firstTweetId = $tweets[0]->getId();
        $tweetsRep = $this->getDoctrine()->getRepository('TweetsBundle:Tweet');

        $vars['previous'] = $tweetsRep
            ->getPreviousTweetId($firstTweetId);
        $vars['next'] = $tweetsRep
            ->getNextTweetId($firstTweetId);

        # Only update the cookie if the last Tweet Id is bigger than
        #  the one in the cookie
        if ($firstTweetId > $vars['cookieId']) {
            $vars['cookie'] = $this->createCookie($firstTweetId);
            $vars['cookieId'] = $firstTweetId;
        }

        $vars['number'] = $tweetsRep
            ->countPendingTweets($vars['cookieId']);

        $vars['first'] = $firstTweetId;

        return ($vars);
    }
}
