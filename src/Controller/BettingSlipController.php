<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\BettingSlipTypeEnum;
use App\Service\BettingSlip\BettingSlipEditorService;
use App\Service\BettingSlip\BettingSlipFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;

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

            return $this->redirectToRoute('app_bankroll_show', [
                'id' => $bs->bankroll?->id,
            ]);
        }

        return $this->render($editor->getTemplates()['new'], [
            'form' => $form->createView(),
        ]);
    }
}
