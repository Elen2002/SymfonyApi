<?php
//
namespace App\Interfaces;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

interface AuthInterface
{
    public function list(): array;
    public function registerOrUpdate(array $params): array;
    public function updateById(array $params): array;
    public function detail(array $params);
    public function detailById(array $params);
    public function detailAlternate(array $params): array;
    public function login(array $params): array;
    public function deleteByEmail(array $params): array;
    public function deleteById(array $params): array;
}
