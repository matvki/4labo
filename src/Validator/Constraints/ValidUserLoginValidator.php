<?php

namespace App\Validator\Constraints;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class ValidUserLoginValidator extends ConstraintValidator
{
    private UserProviderInterface       $userProvider;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userProvider     = $userProvider;
        $this->passwordHasher   = $passwordHasher;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidUserLogin) {
            throw new UnexpectedTypeException($constraint, ValidUserLogin::class);
        }

        $username = $value['_username'] ?? null;
        $password = $value['_password'] ?? null;

        if ($username && $password) {
            try {
                $user = $this->userProvider->loadUserByIdentifier($username);
                if ($user && $this->passwordHasher->isPasswordValid($user, $password))
                    return;
            } catch (UsernameNotFoundException $e) {
                // Handle exception if user not found
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $username)
            ->addViolation();
    }
}