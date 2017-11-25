<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TrackElem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Trackelem controller.
 *
 * @Route("trackelem")
 */
class TrackElemController extends Controller
{
    /**
     * Lists all trackElem entities.
     *
     * @Route("/", name="trackelem_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trackElems = $em->getRepository('AppBundle:TrackElem')->findAll();

        return $this->render('trackelem/index.html.twig', array(
            'trackElems' => $trackElems,
        ));
    }

    /**
     * Creates a new trackElem entity.
     *
     * @Route("/new", name="trackelem_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $trackElem = new Trackelem();
        $form = $this->createForm('AppBundle\Form\TrackElemType', $trackElem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trackElem);
            $em->flush();

            return $this->redirectToRoute('trackelem_show', array('id' => $trackElem->getId()));
        }

        return $this->render('trackelem/new.html.twig', array(
            'trackElem' => $trackElem,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a trackElem entity.
     *
     * @Route("/{id}", name="trackelem_show")
     * @Method("GET")
     */
    public function showAction(TrackElem $trackElem)
    {
        $deleteForm = $this->createDeleteForm($trackElem);

        return $this->render('trackelem/show.html.twig', array(
            'trackElem' => $trackElem,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing trackElem entity.
     *
     * @Route("/{id}/edit", name="trackelem_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TrackElem $trackElem)
    {
        $deleteForm = $this->createDeleteForm($trackElem);
        $editForm = $this->createForm('AppBundle\Form\TrackElemType', $trackElem);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trackelem_edit', array('id' => $trackElem->getId()));
        }

        return $this->render('trackelem/edit.html.twig', array(
            'trackElem' => $trackElem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a trackElem entity.
     *
     * @Route("/{id}", name="trackelem_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TrackElem $trackElem)
    {
        $form = $this->createDeleteForm($trackElem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trackElem);
            $em->flush();
        }

        return $this->redirectToRoute('trackelem_index');
    }

    /**
     * Creates a form to delete a trackElem entity.
     *
     * @param TrackElem $trackElem The trackElem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TrackElem $trackElem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('trackelem_delete', array('id' => $trackElem->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
