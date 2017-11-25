<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Racecategory controller.
 *
 * @Route("racecategory")
 */
class RaceCategoryController extends Controller
{
    /**
     * Lists all raceCategory entities.
     *
     * @Route("/", name="racecategory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $raceCategories = $em->getRepository('AppBundle:RaceCategory')->findAll();

        return $this->render('racecategory/index.html.twig', array(
            'raceCategories' => $raceCategories,
        ));
    }

    /**
     * Creates a new raceCategory entity.
     *
     * @Route("/new", name="racecategory_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $raceCategory = new Racecategory();
        $form = $this->createForm('AppBundle\Form\RaceCategoryType', $raceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($raceCategory);
            $em->flush();

            return $this->redirectToRoute('racecategory_show', array('id' => $raceCategory->getId()));
        }

        return $this->render('racecategory/new.html.twig', array(
            'raceCategory' => $raceCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a raceCategory entity.
     *
     * @Route("/{id}", name="racecategory_show")
     * @Method("GET")
     */
    public function showAction(RaceCategory $raceCategory)
    {
        $deleteForm = $this->createDeleteForm($raceCategory);

        return $this->render('racecategory/show.html.twig', array(
            'raceCategory' => $raceCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing raceCategory entity.
     *
     * @Route("/{id}/edit", name="racecategory_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RaceCategory $raceCategory)
    {
        $deleteForm = $this->createDeleteForm($raceCategory);
        $editForm = $this->createForm('AppBundle\Form\RaceCategoryType', $raceCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('racecategory_edit', array('id' => $raceCategory->getId()));
        }

        return $this->render('racecategory/edit.html.twig', array(
            'raceCategory' => $raceCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a raceCategory entity.
     *
     * @Route("/{id}", name="racecategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, RaceCategory $raceCategory)
    {
        $form = $this->createDeleteForm($raceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($raceCategory);
            $em->flush();
        }

        return $this->redirectToRoute('racecategory_index');
    }

    /**
     * Creates a form to delete a raceCategory entity.
     *
     * @param RaceCategory $raceCategory The raceCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RaceCategory $raceCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('racecategory_delete', array('id' => $raceCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    /**
     * Lists all raceCategory entities.
     *
     * @Route("/index/json", name="racecategory_index_json")
     * @Method("GET")
     */
    public function indexJSONAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $resArray = [];
        $q = $request->get('q') ?  $request->get('q') : '';
        $categories = $em->getRepository('AppBundle:RaceCategory')->createQueryBuilder('c')
                        ->where('c.name LIKE :q')
                        ->setParameter('q', '%'.$q.'%')
                        ->getQuery()->getResult();

        foreach($categories as $category){
            $resArray[] = array(
                'id' => $category->getId(),
                'text' => $category->getName()
            );
        }

        return new JsonResponse($resArray);
    }
}
