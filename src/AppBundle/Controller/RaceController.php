<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCategory;
use AppBundle\Entity\RaceRun;
use AppBundle\Entity\RaceRunner;
use AppBundle\Entity\Track;
use AppBundle\Entity\TrackElem;
use AppBundle\Entity\TrackPoint;
use AppBundle\Form\EditRaceType;
use AppBundle\Form\SignForRaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Race controller.
 *
 * @Route("race")
 */
class RaceController extends Controller
{
    /**
     * Lists all race entities.
     *
     * @Route("/", name="race_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $races = $em->getRepository('AppBundle:Race')->findAll();

        return $this->render('race/index.html.twig', array(
            'races' => $races,
        ));
    }

    /**
     * Creates a new race entity.
     *
     * @Route("/new", name="race_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $race = new Race();
        $form = $this->createForm('AppBundle\Form\RaceType', $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            return $this->redirectToRoute('race_show', array('id' => $race->getId()));
        }

        return $this->render('race/new.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a race entity.
     *
     * @Route("/show/{id}", name="race_show")
     * @Method("GET")
     */
    public function showAction(Race $race)
    {
        return $this->render('race/show.html.twig', array(
            'race' => $race,
            'form' => $this->createSignForm($race)->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing race entity.
     *
     * @Route("/edit/{id}", name="race_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Race $race)
    {
        $deleteForm = $this->createDeleteForm($race);
        $editForm = $this->createForm('AppBundle\Form\RaceType', $race);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('race_edit', array('id' => $race->getId()));
        }

        return $this->render('race/edit.html.twig', array(
            'race' => $race,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a race entity.
     *
     * @Route("/delete/{id}", name="race_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Race $race)
    {
        if($race->getContest()->getCompany() != $this->getUser()->getCompany() && !$this->getUser()->hasRole('ROLE_ADMIN') ){
            return $this->redirectToRoute('company_contests');
        }

        if($race->getStartTime() < new \DateTime()){
            $this->addFlash('warning', 'Nie możesz usunąć wyścigów, której już się zaczęły!!');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $form = $this->createDeleteForm($race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($race);
            $em->flush();
        }

        return $this->redirectToRoute('race_index');
    }

    /**
     * Creates a form to delete a race entity.
     *
     * @param Race $race The race entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Race $race)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('race_delete', array('id' => $race->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates a new contest entity.
     *
     * @Route("/add-race/{id}", name="race_add")
     * @Method({"GET", "POST"})
     */
    public function addRaceAction(Request $request, Contest $contest)
    {
        $race = new Race();
        $race->setContest($contest);

        $form = $this->createForm(EditRaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->addNewTrackFromJSON(json_decode($form->get('route')->getData(), true), $race);
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie dodany do zawodów!');
            return $this->redirectToRoute('contest_show', array('id' => $contest->getId()));
        }

        return $this->render('race/add.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit race entity.
     *
     * @Route("/edit-race/{id}", name="race_edit")
     * @Method({"GET", "POST"})
     */
    public function editRaceAction(Request $request, Race $race)
    {
        if($race->getContest()->getCompany() != $this->getUser()->getCompany() && !$this->getUser()->hasRole('ROLE_ADMIN') ){
            return $this->redirectToRoute('company_contests');
        }

        if($race->getStartTime() < new \DateTime()){
            $this->addFlash('warning', 'Nie możesz edytować wyścigów, której już się zaczęły!!');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $form = $this->createForm(EditRaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->editTrackFromJSON(json_decode($form->get('route')->getData(), true), $race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie edytowany!');
            return $this->redirectToRoute('contest_edit', array('id' => $race->getContest()->getId()));
        }

        return $this->render('race/edit.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }

    public function addNewTrackFromJSON($routePoints, Race $race){
        $em = $this->getDoctrine()->getManager();
        $track = new Track();
        $track->setName('TRASA - '.$race->getName());
        $track->addRace($race);

        $trackElem = new TrackElem();
        $track->addTrackElems($trackElem);

        foreach($routePoints as $key => $point){
            $trackElem->addPoint(new TrackPoint($key, $point['lat'], $point['lng']));
        }

        $em->persist($track);
    }

    public function editTrackFromJSON($routePoints, Race $race){
        $em = $this->getDoctrine()->getManager();

        $trackElem = $race->getTrack()->getTrackElems()[0];
        foreach ($trackElem->getPoints() as $point){
            $em->remove($point);
        }

        foreach($routePoints as $key => $point){
            $trackElem->addPoint(new TrackPoint($key, $point['lat'], $point['lng']));
        }

        $em->flush();
    }

    /**
     * @param Race $race
     * @return \Symfony\Component\Form\Form
     */
    public function createSignForm(Race $race){
        return  $this->createForm(SignForRaceType::class, new RaceRunner(), array(
            'race' => $race,
        ));
    }

    /**
     * Display live race.
     *
     * @Route("/live/{id}", name="race_live")
     * @Method("GET")
     */
    public function liveAction(Race $race)
    {
        return $this->render('race/live.html.twig', array(
            'race' => $race
        ));
    }

    /**
     * Display preview live race.
     *
     * @Route("/preview/{id}", name="race_preview")
     * @Method("GET")
     */
    public function previewAction(Race $race)
    {
        return $this->render('race/preview.html.twig', array(
            'race' => $race,
            'location_data' => $this->prepareAllLocationData($race)
        ));
    }

    /**
     * Set cords for runner race.
     *
     * @Route("/set-cords/{hashid}", name="race_set_cords")
     * @Method("GET")
     */
    public function setCordsAction(Request $request, RaceRunner $raceRunner)
    {
        $em = $this->getDoctrine()->getManager();

        if(!$raceRunner->getEndTime()){
            $raceRun = new RaceRun();
            $raceRun->setTime(new \DateTime());
            $raceRun->setLat($request->get('lat'));
            $raceRun->setLng($request->get('lng'));
            $raceRunner->addRaceRun($raceRun);

            $em->flush();

            if($this->isRunnerEnded($raceRunner)){
                $raceRunner->setStartTime($raceRunner->getRace()->getStartTime());
                $raceRunner->setEndTime($raceRun->getTime());
                $em->flush();

                $this->calcPlaces($raceRunner->getRace());
            }
        }

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT RR.*, RaRu.runner_id
            FROM race_run RR, race_runner RaRu
            WHERE RR.time = (
                SELECT MAX(RR2.time)
                FROM race_run RR2
                WHERE RR2.race_runner_id = RR.race_runner_id
            )
            AND 
            (
                RR.race_runner_id = RaRu.id
                AND
                RaRu.race_id = :race_id
            )");
        $statement->bindValue('race_id', $raceRunner->getRace()->getId());
        $statement->execute();
        $results = $statement->fetchAll();

        $resultsToSave = [];
        foreach ($results as $result){
            $resultsToSave[] = array(
                'run_point_id' => $result['id'],
                'id' => $result['runner_id'],
                'lat' => $result['lat'],
                'lng' => $result['lng'],
                'timestamp' => (new \DateTime($result['time']))->getTimestamp()
            );
        }

        $statement = $connection->prepare("
            DELETE FROM live WHERE race_id = :race_id;
            INSERT INTO `live`(`race_id`, `positions`) VALUES (:race_id, :positions);
        ");
        $statement->bindValue('race_id', $raceRunner->getRace()->getId());
        $statement->bindValue('positions', json_encode($resultsToSave));
        $statement->execute();

        return new Response('ok');
    }

    public function isRunnerEnded(RaceRunner $raceRunner){
        $em = $this->getDoctrine()->getManager();

        $lastCords = $em->getRepository(RaceRun::class)
                        ->createQueryBuilder('rr')
                        ->where('rr.raceRunner = :race_runner')
                        ->setParameter('race_runner', $raceRunner)
                        ->orderBy('rr.time', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()->getResult()[0];

        $lastCordsOfTrack = $em->getRepository(TrackPoint::class)
                                ->createQueryBuilder('tp')
                                ->join('tp.trackElem', 'te')
                                ->join('te.track', 't')
                                ->where('t.id = :track_id')
                                ->setParameter('track_id', $raceRunner->getRace()->getTrack()->getId())
                                ->orderBy('tp.orderNumber', 'DESC')
                                ->setMaxResults(1)
                                ->getQuery()->getResult()[0];

        return 2 > $this->calcDistance($lastCords->getLat(), $lastCords->getLng(), $lastCordsOfTrack->getLat(), $lastCordsOfTrack->getLng());

    }

    public static function calcDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);

        return acos($dist) / $rad * 60 *  1852;
    }

    public function calcPlaces(Race $race){
        $em = $this->getDoctrine()->getManager();

        $raceRunners = $em->getRepository(RaceRunner::class)
                        ->createQueryBuilder('rr')
                        ->where('rr.race = :race')
                        ->setParameter('race', $race)
                        ->andWhere('rr.endTime IS NOT NULL')
                        ->orderBy('rr.endTime', 'ASC')
                        ->getQuery()->getResult();

        foreach ( $raceRunners as $key => $raceRunner) {
            $raceRunner->setFinishPlace($key+1);
        }

        $em->flush();
    }

    public static function prepareDataForStatistic(RaceRunner $raceRunner, $granulation = 100){
        $results = array(
            array(
                'meters' => 0,
                'time' => 0,
                'speed' => 0,
            )
        );
        $tmpD = 0;
        $tmpT = 0;
        $tmpIndex = 1;
        $points = $raceRunner->getRaceRun()->toArray();
        for($i = 1; $i < count($points); $i++){
            $p1 = $points[$i-1];
            $p2 = $points[$i];
            $tmpD += RaceController::calcDistance($p1->getLat(), $p1->getLng(), $p2->getLat(), $p2->getLng());
            $tmpT += abs($p1->getTime()->getTimeStamp() - $p2->getTime()->getTimeStamp());
            while($tmpD > $granulation){
                $results[] = array(
                    'meters' => ($tmpIndex * $granulation),
                    'time' => ($granulation * $tmpT)/$tmpD,
                    'speed' => round(($tmpD/$tmpT) * 3.6, 2),
                );
                $tmpIndex++;
                $tmpT -= end($results)['time'];
                $tmpD -= $granulation;
            }
        }
        if($tmpD > 0){
            $results[] = array(
                'meters' => round($tmpD + (($tmpIndex-1) * $granulation), 2),
                'time' => ($granulation * $tmpT)/$tmpD,
                'speed' => 0// round(($tmpD/$tmpT) * 3.6, 2),
            );
        }

        return $results;
    }

    public function prepareAllLocationData(Race $race){
        if(!$race->isEnded()){
            return json_encode('');
        }
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $startTime = $race->getRealStartTime()->getTimestamp();
        $endTime = $race->getEndTime()->getTimestamp();
        $currentTime = $startTime;
        $i = 0;
        $interval = 2;
        $fullResults = [];
        while($currentTime <= $endTime){
            $statement = $connection->prepare("SELECT RR.*, RaRu.runner_id
            FROM race_run RR, race_runner RaRu
            WHERE RR.time = (
                SELECT MAX(RR2.time)
                FROM race_run RR2
                WHERE RR2.race_runner_id = RR.race_runner_id
                  AND RR2.time <= :max_accepted_time
            )
            AND 
            (
                RR.race_runner_id = RaRu.id
                AND
                RaRu.race_id = :race_id
            )");
            $statement->bindValue('race_id', $race->getId());
            $statement->bindValue('max_accepted_time', date('Y-m-d H:i:s', $currentTime));
            $statement->execute();
            $results = $statement->fetchAll();

            $fullResults[$i] = array(
                'time' => $currentTime - $startTime,
                'points' => [],
            );
            foreach ($results as $result){
                $fullResults[$i]['points'][] = array(
                    'run_point_id' => $result['id'],
                    'id' => $result['runner_id'],
                    'lat' => $result['lat'],
                    'lng' => $result['lng'],
                    'timestamp' => (new \DateTime($result['time']))->getTimestamp()
                );
            }

            $currentTime = $currentTime + $interval;
            $i++;
        }

        return $fullResults;
    }
}
