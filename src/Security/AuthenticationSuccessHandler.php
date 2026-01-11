<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
{
    $user = $token->getUser();
    
    error_log('User roles: ' . print_r($user->getRoles(), true));
    
    if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
        error_log('Redirecting admin to dashboard');
        return new RedirectResponse($this->router->generate('admin_dashboard'));
    }
    
    error_log('Redirecting normal user to home');
    return new RedirectResponse($this->router->generate('app_home'));
}
}
