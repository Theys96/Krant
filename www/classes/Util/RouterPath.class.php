<?php
namespace Util;

use Controller\Response;

class RouterPath
{
    private Response $response;

    private array $roles;

    public function __construct(Response $response, array $roles)
    {
        $this->response = $response;
        $this->roles = $roles;
    }

    public function is_allowed(int $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function render(): string
    {
        return $this->response->render();
    }
}
?>
