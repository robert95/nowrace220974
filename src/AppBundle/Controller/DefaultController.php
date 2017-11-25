<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use AppBundle\Entity\RaceRunner;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if($user){
            if($user->hasRole("ROLE_RUNNER")){
                return $this->render(':dashboard:dashboard_runner.html.twig');
            }else if($user->hasRole("ROLE_COMPANY")){
                return $this->render(':dashboard:dashboard_company.html.twig');
            }else if($user->hasRole("ROLE_ADMIN")){
                return $this->redirectToRoute('user_index');
            }
        }else{
            $em = $this->getDoctrine()->getManager();
            $incommingRaces = [];
            $endedRaces = [];
            $races = $em->getRepository(Race::class)->createQueryBuilder('r')
                        ->orderBy('r.startTime', 'ASC')
                        ->getQuery()->getResult();
            foreach ($races as $race){
                if($race->isEnded()){
                    $endedRaces[] = $race;
                }else{
                    $incommingRaces[] = $race;
                }
            }
            return $this->render(':default:homepage.html.twig', array(
                'incommingRaces' => $incommingRaces,
                'endedRaces' => array_reverse($endedRaces, true),
            ));
        }
    }
}
