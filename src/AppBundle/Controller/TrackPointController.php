<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TrackPoint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Trackpoint controller.
 *
 * @Route("trackpoint")
 */
class TrackPointController extends Controller
{
    /**
     * Lists all trackPoint entities.
     *
     * @Route("/", name="trackpoint_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trackPoints = $em->getRepository('AppBundle:TrackPoint')->findAll();

        return $this->render('trackpoint/index.html.twig', array(
            'trackPoints' => $trackPoints,
        ));
    }

    /**
     * Creates a new trackPoint entity.
     *
     * @Route("/new", name="trackpoint_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $trackPoint = new Trackpoint();
        $form = $this->createForm('AppBundle\Form\TrackPointType', $trackPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trackPoint);
            $em->flush();

            return $this->redirectToRoute('trackpoint_show', array('id' => $trackPoint->getId()));
        }

        return $this->render('trackpoint/new.html.twig', array(
            'trackPoint' => $trackPoint,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a trackPoint entity.
     *
     * @Route("/{id}", name="trackpoint_show")
     * @Method("GET")
     */
    public function showAction(TrackPoint $trackPoint)
    {
        $deleteForm = $this->createDeleteForm($trackPoint);

        return $this->render('trackpoint/show.html.twig', array(
            'trackPoint' => $trackPoint,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing trackPoint entity.
     *
     * @Route("/{id}/edit", name="trackpoint_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TrackPoint $trackPoint)
    {
        $deleteForm = $this->createDeleteForm($trackPoint);
        $editForm = $this->createForm('AppBundle\Form\TrackPointType', $trackPoint);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trackpoint_edit', array('id' => $trackPoint->getId()));
        }

        return $this->render('trackpoint/edit.html.twig', array(
            'trackPoint' => $trackPoint,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a trackPoint entity.
     *
     * @Route("/{id}", name="trackpoint_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TrackPoint $trackPoint)
    {
        $form = $this->createDeleteForm($trackPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trackPoint);
            $em->flush();
        }

        return $this->redirectToRoute('trackpoint_index');
    }

    /**
     * Creates a form to delete a trackPoint entity.
     *
     * @param TrackPoint $trackPoint The trackPoint entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TrackPoint $trackPoint)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('trackpoint_delete', array('id' => $trackPoint->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
