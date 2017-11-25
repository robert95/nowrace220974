<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceRun;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Racerun controller.
 *
 * @Route("racerun")
 */
class RaceRunController extends Controller
{
    /**
     * Lists all raceRun entities.
     *
     * @Route("/", name="racerun_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $raceRuns = $em->getRepository('AppBundle:RaceRun')->findAll();

        return $this->render('racerun/index.html.twig', array(
            'raceRuns' => $raceRuns,
        ));
    }

    /**
     * Creates a new raceRun entity.
     *
     * @Route("/new", name="racerun_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $raceRun = new Racerun();
        $form = $this->createForm('AppBundle\Form\RaceRunType', $raceRun);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($raceRun);
            $em->flush();

            return $this->redirectToRoute('racerun_show', array('id' => $raceRun->getId()));
        }

        return $this->render('racerun/new.html.twig', array(
            'raceRun' => $raceRun,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a raceRun entity.
     *
     * @Route("/{id}", name="racerun_show")
     * @Method("GET")
     */
    public function showAction(RaceRun $raceRun)
    {
        $deleteForm = $this->createDeleteForm($raceRun);

        return $this->render('racerun/show.html.twig', array(
            'raceRun' => $raceRun,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing raceRun entity.
     *
     * @Route("/{id}/edit", name="racerun_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RaceRun $raceRun)
    {
        $deleteForm = $this->createDeleteForm($raceRun);
        $editForm = $this->createForm('AppBundle\Form\RaceRunType', $raceRun);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('racerun_edit', array('id' => $raceRun->getId()));
        }

        return $this->render('racerun/edit.html.twig', array(
            'raceRun' => $raceRun,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a raceRun entity.
     *
     * @Route("/{id}", name="racerun_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, RaceRun $raceRun)
    {
        $form = $this->createDeleteForm($raceRun);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($raceRun);
            $em->flush();
        }

        return $this->redirectToRoute('racerun_index');
    }

    /**
     * Creates a form to delete a raceRun entity.
     *
     * @param RaceRun $raceRun The raceRun entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RaceRun $raceRun)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('racerun_delete', array('id' => $raceRun->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
