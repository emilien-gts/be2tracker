<?php

namespace App\Service\Bankroll\BettingSlip;

use App\Entity\Bankroll\BettingSlip;
use App\Service\Bankroll\BettingSlip\Editor\AbstractBettingSlipEditor;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class BettingSlipEditorService
{
    /**
     * @var iterable<int, AbstractBettingSlipEditor>
     */
    private readonly iterable $editors;

    public function __construct(
        #[AutowireIterator('app.betting_slip_editor')] iterable $editors,
    ) {
        $this->editors = $editors;
    }

    public function getEditor(BettingSlip $bs): AbstractBettingSlipEditor
    {
        foreach ($this->editors as $editor) {
            if ($editor->supportBettingSlip($bs)) {
                return $editor;
            }
        }

        throw new \InvalidArgumentException(\sprintf('No editor found for betting slip %s', $bs->__toString()));
    }
}
