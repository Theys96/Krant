<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Util\ViewRenderer;

class Categories extends LoggedIn
{
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.categories', [
            'categories' => Category::getAll()
        ]);
    }

    public function allowed_roles(): array
    {
       return [3];
    }
}
