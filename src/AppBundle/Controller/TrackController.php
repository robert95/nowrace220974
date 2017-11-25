<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Track controller.
 *
 * @Route("track")
 */
class TrackController extends Controller
{
    /**
     * Lists all track entities.
     *
     * @Route("/", name="track_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tracks = $em->getRepository('AppBundle:Track')->findAll();

        return $this->render('track/index.html.twig', array(
            'tracks' => $tracks,
        ));
    }

    /**
     * Creates a new track entity.
     *
     * @Route("/new", name="track_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $track = new Track();
        $form = $this->createForm('AppBundle\Form\TrackType', $track);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($track);
            $em->flush();

            return $this->redirectToRoute('track_show', array('id' => $track->getId()));
        }

        return $this->render('track/new.html.twig', array(
            'track' => $track,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a track entity.
     *
     * @Route("/{id}", name="track_show")
     * @Method("GET")
     */
    public function showAction(Track $track)
    {
        $deleteForm = $this->createDeleteForm($track);

        return $this->render('track/show.html.twig', array(
            'track' => $track,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing track entity.
     *
     * @Route("/{id}/edit", name="track_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Track $track)
    {
        $deleteForm = $this->createDeleteForm($track);
        $editForm = $this->createForm('AppBundle\Form\TrackType', $track);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('track_edit', array('id' => $track->getId()));
        }

        return $this->render('track/edit.html.twig', array(
            'track' => $track,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a track entity.
     *
     * @Route("/{id}", name="track_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Track $track)
    {
        $form = $this->createDeleteForm($track);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($track);
            $em->flush();
        }

        return $this->redirectToRoute('track_index');
    }

    /**
     * Creates a form to delete a track entity.
     *
     * @param Track $track The track entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Track $track)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('track_delete', array('id' => $track->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
