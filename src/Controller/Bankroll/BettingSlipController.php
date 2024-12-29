<?php

declare(strict_types=1);

namespace App\Controller\Bankroll;

use App\Entity\Bankroll\BettingSlip;
use App\Enum\Bankroll\BettingSlipTypeEnum;
use App\Service\Bankroll\BettingSlip\BettingSlipEditorService;
use App\Service\Bankroll\BettingSlip\BettingSlipFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/betting-slip')]
final class BettingSlipController extends AbstractController
{
    public function __construct(
        private readonly BettingSlipEditorService $service,
        private readonly BettingSlipFactory $factory,
    ) {
    }

    #[Route('/chose-type')]
    public function choseType(): Response
    {
        return $this->render('betting_slip/chose_type.html.twig');
    }

    #[Route('/new/{type}', requirements: ['type' => new EnumRequirement(BettingSlipTypeEnum::class)])]
    public function new(Request $request, BettingSlipTypeEnum $type): Response
    {
        $bs = $this->factory->create($type);
        $editor = $this->service->getEditor($bs);
        $form = $editor->createForm($bs);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $editor->save($bs);

            return $this->redirectToRoute('app_bankroll_bankroll_show', [
                'id' => $bs->bankroll?->id,
            ]);
        }

        return $this->render($editor->getTemplates()['new'], [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', requirements: ['id' => Requirement::UUID_V7])]
    public function edit(Request $request, BettingSlip $bs): Response
    {
        $editor = $this->service->getEditor($bs);
        $form = $editor->createForm($bs);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $editor->save($bs);

            return $this->redirectToRoute('app_bankroll_bankroll_bets', [
                'id' => $bs->bankroll?->id,
            ]);
        }

        return $this->render($editor->getTemplates()['edit'], [
            'form' => $form->createView(),
            'bs' => $bs,
        ]);
    }

    #[Route('/delete/{id}', requirements: ['id' => Requirement::UUID_V7])]
    public function delete(BettingSlip $bs): Response
    {
        $editor = $this->service->getEditor($bs);
        $editor->delete($bs);

        return $this->redirectToRoute('app_bankroll_bankroll_bets', [
            'id' => $bs->bankroll?->id,
        ]);
    }
}
