<?php

namespace App\Controller;

use App\Form\BetCombinationType;
use App\Model\BetCombination;
use App\Service\BetCombinationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bet-combination')]
class BetCombinationController extends AbstractController
{
    public function __construct(private readonly BetCombinationService $service)
    {
    }

    #[Route('/index')]
    public function index(Request $request): Response
    {
        $combination = new BetCombination();
        $form = $this->createForm(BetCombinationType::class, $combination);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->combine($combination);
        }

        return $this->render('bet_combination/index.html.twig', [
            'form' => $form->createView(),
            'combination' => $combination,
        ]);
    }
}
