<?php

declare(strict_types=1);

namespace App\Jfcherng\Diff\Renderer\Html\LineRenderer;

use App\Jfcherng\Utility\MbString;

final class None extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function render(MbString $mbOld, MbString $mbNew): LineRendererInterface
    {
        return $this;
    }
}
