<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController
{
    public function __invoke(#[CurrentUser] $user = null): ?User
    {
        if (!$user) {
            throw new NotFoundHttpException('No user logged in.');
        }

        return $user;
    }
}
