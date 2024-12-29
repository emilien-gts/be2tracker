<?php

namespace App\Controller\Bankroll;

use App\Entity\Bankroll\Bankroll;
use App\Form\Bankroll\BankrollType;
use App\Repository\Bankroll\BankrollRepository;
use App\Service\Bankroll\BankrollService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/bankroll')]
class BankrollController extends AbstractController
{
    public function __construct(
        private readonly BankrollRepository $repository,
        private readonly BankrollService $service,
    ) {
    }

    #[Route('/index')]
    public function index(): Response
    {
        return $this->render('bankroll/index.html.twig', [
            'bankrolls' => $this->repository->findAll(),
        ]);
    }

    #[Route('/new')]
    public function new(Request $request): Response
    {
        $bankroll = $this->service->create();
        $form = $this->createForm(BankrollType::class, $bankroll);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->save($bankroll);

            return $this->redirectToRoute('app_bankroll_bankroll_show', [
                'id' => $bankroll->id,
            ]);
        }

        return $this->render('bankroll/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', requirements: ['id' => Requirement::UUID_V7])]
    public function show(Bankroll $bankroll): Response
    {
        return $this->render('bankroll/show.html.twig', [
            'bankroll' => $bankroll,
            'chart' => $this->service->buildChart($bankroll),
        ]);
    }

    #[Route('/{id}/bets', requirements: ['id' => Requirement::UUID_V7])]
    public function bets(Bankroll $bankroll): Response
    {
        return $this->render('bankroll/bets.html.twig', [
            'bankroll' => $bankroll,
            'periods' => $this->service->getBankrollPeriods($bankroll),
        ]);
    }

    #[Route('/delete/{id}', requirements: ['id' => Requirement::UUID_V7])]
    public function delete(Bankroll $bankroll): Response
    {
        $this->service->delete($bankroll);

        return $this->redirectToRoute('app_bankroll_bankroll_index');
    }
}
