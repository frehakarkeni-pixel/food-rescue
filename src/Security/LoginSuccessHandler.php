<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private UrlGeneratorInterface $router)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames();

        $route = match (true) {
            in_array('ROLE_ADMIN', $roles, true) => 'app_admin_dashboard',
            in_array('ROLE_COMMERCE', $roles, true) => 'app_commerce_home',
            default => 'app_client_home',
        };

        return new RedirectResponse($this->router->generate($route));
    }
}