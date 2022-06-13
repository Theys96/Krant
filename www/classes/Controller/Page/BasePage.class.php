<?php
namespace Controller\Page;

use Controller\Response;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

abstract class BasePage implements Response
{
    abstract function get_body(): string;

    public function render(): string
    {
        return ViewRenderer::render_view('base', [
            'body' => $this->get_body()
        ]);
    }
}
?>
