<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class CustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private UserPasswordHasherInterface $passwordHasher;
    private UserProviderInterface $userProvider;
    private RouterInterface $router;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserProviderInterface $userProvider, RouterInterface $router)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userProvider = $userProvider;
        $this->router = $router;
    }

    public function authenticate(Request $request): Passport
    {
        $email      = $request->request->all()['login_form']['_username'];
        $password   = $request->request->all()['login_form']['_password'];

        if (null === $email || null === $password) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                return $this->userProvider->loadUserByIdentifier($userIdentifier);
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect to the target path or default homepage
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_login');
    }
}
