<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PlayerFormType;
use App\Entity\Player;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $player = new Player();
        $form = $this->createForm(PlayerFormType::class, $player);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($player);
            $em->flush();
        }

        $players = $em->getRepository(Player::class)->findAll();

        return $this->render('default/index.html.twig', [
            'players' => $players,
            'add_player' => $form->createView()
        ]);
    }

    /**
     * @Route("/player/{id}", name="player")
     */
    public function player(Player $player, Request $request){
        $form = $this->createForm(PlayerFormType::class, $player);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();
        }

        return $this->render('default/player.html.twig', 
            array(
                'player' => $player,
                'edit_form' => $form->createView()
            )
        );
    }

    /**
     * @Route("/player/delete/{id}", name="playerDelete")
     */
    public function playerDelete(Player $player = null){
        if ($player != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
