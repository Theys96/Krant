<?php
namespace Controller\Page;

use Controller\Response;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

/**
 * Basis voor elke pagina.
 */
abstract class BasePage implements Response
{
    /**
     * Inhoud (<body>)
     *
     * @return string
     */
    abstract function get_body(): string;

    /**
     * @return string
     */
    public function render(): string
    {
        return ViewRenderer::render_view('base', [
            'errors' => ErrorHandler::instance()->printAllToString(),
            'body' => $this->get_body()
        ]);
    }
}
