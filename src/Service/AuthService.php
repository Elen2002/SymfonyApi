<?php

namespace App\Service;


use App\Entity\User;
use App\Interfaces\AuthInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService implements AuthInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ParameterBagInterface
     */
    private $bag;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $bag, UserRepository $userRepository, UserPasswordHasherInterface $encoder)
    {
        $this->em = $em;
        $this->bag = $bag;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    public function list(): array{
        return $this->userRepository->list();
    }

    public function registerOrUpdate(array $params): array
    {
        $response ['status'] = false;
        if (empty($params['email'])) {
            $response ['message'] = "Email is missing";
            return $response;
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $response["message"] = 'Email is invalid!';
            return $response;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'email' => $params['email'],
        ]);

        if (empty($user)) {
            $user = new User();
            $user->setEmail($params['email']);
            $this->em->persist($user);

            $payload = [
                "user" => $user->getUserIdentifier(),
                "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
            ];

            $jwt = JWT::encode($payload, $this->bag->get('jwt_secret'), 'HS256');
            $user->setToken($jwt);
        } else {
            if (!$this->encoder->isPasswordValid($user, $params['oldPassword'])) {
                $response ['message'] = "Old password is wrong";
                return $response;
            }
        }
        if (!empty($params['password'])){
            $user->setPassword($this->encoder->hashPassword($user, $params['password']));
        }
        if (empty($params['username'])) {
            $username = explode('@', $params['email'])[0];
        } else {
            $username = $params['username'];
        }
        $user->setUsername($username);
        if (!empty($params['role'])) {
            $role = ['ROLE_USER', 'ROLE_ADMIN'];
        } else {
            $role = ['ROLE_USER'];
        }
        $user->setRoles($role);

        $this->em->persist($user);
        $this->em->flush();
        if ($user) {
            $response['status'] = true;
        }

        return $response;
    }

    public function updateById(array $params): array
    {
        $response ['status'] = false;
        if (empty($params['id'])) {
            $response ['message'] = "Id is missing";
            return $response;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'id' => $params['id'],
        ]);
        if (!empty($params['password'])){
            $user->setPassword($this->encoder->hashPassword($user, $params['password']));
        }
        if (empty($params['username']) && !empty($params['email'])) {
            $username = explode('@', $params['email'])[0];
        } else {
            $username = $params['username'];
        }
        $user->setUsername($username);
        $role = ['ROLE_USER'];
        $user->setRoles($role);

        $this->em->persist($user);
        $this->em->flush();
        if ($user) {
            $response['status'] = true;
            $response["message"] = 'User ' . $user->getEmail() . ' updated successfully';
        }

        return $response;
    }

    public function detail(array $params): array
    {
        $response ['status'] = false;
        if (empty($params['email'])) {
            $response ['message'] = "Email is missing";
            return $response;
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $response["message"] = 'Email is invalid!';
            return $response;
        }
        $user = $this->userRepository->findOneBy(['email' => $params['email']]);
        if (empty($user)) {
            $response ['message'] = "User not found";
        } else {
            $response['status'] = true;
            $response['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'token' => $user->getToken(),
            ];
        }
        return $response;
    }

    public function detailById(array $params): array
    {
        $response ['status'] = false;
        if (empty($params['id'])) {
            $response ['message'] = "id is missing";
            return $response;
        }
        $user = $this->userRepository->getUser(['id'=>$params['id']]);
        if (empty($user)) {
            $response ['message'] = "User not found";
        } else {
            $response['user'] = $user;
            $response['status'] = true;
        }
        return $response;
    }

    public function detailAlternate(array $params): array
    {
        $response ['status'] = false;
        if (empty($params['email'])) {
            $response ['message'] = "Email is missing";
            return $response;
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $response["message"] = 'Email is invalid!';
            return $response;
        }
        $user = $this->userRepository->getUser(['email' => $params['email']]);
        if (empty($user)) {
            $response ['message'] = "User not found";
        } else {
            $response['status'] = true;
            $response['user'] = $user;
        }
        return $response;
    }

    public function login(array $params): array
    {
        $response["status"] = false;
        if (empty($params['email']) || empty($params['password'])) {
            $response["message"] = 'Email or password is empty!';
            return $response;
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $response["message"] = 'Email is invalid!';
            return $response;
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $params['email']]);
        if (empty($user)) {
            $response["message"] = 'No user found with this email.';
            return $response;
        }
        if (!empty($user) && !$this->encoder->isPasswordValid($user, $params['password'])) {
            $response["message"] = 'The email or password is incorrect!';
            return $response;
        }
        $response["status"] = true;
        $response["message"] = $user->getEmail() . ' User is login';
        $response["token"] = $user->getToken();
        return $response;
    }

    public function deleteByEmail(array $params): array
    {
        $response["status"] = false;
        if (empty($params['email'])) {
            $response ['message'] = "Email is missing";
            return $response;
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $params['email']]);
        if (empty($user)) {
            $response ['message'] = "User not found";
            return $response;
        }
        if (!empty($user) && !$this->encoder->isPasswordValid($user, $params['password'])) {
            $response["message"] = 'The email or password is incorrect!';
            return $response;
        }

        $this->em->remove($user);
        $this->em->flush();
        $response['status'] = true;
        $response['message'] = "User deleted successfully";
        return $response;
    }

    public function deleteById(array $params): array
    {
        $response["status"] = false;
        if (empty($params['id'])) {
            $response ['message'] = "id is missing";
            return $response;
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['id' => $params['id']]);
        if (empty($user)) {
            $response ['message'] = "User not found";
            return $response;
        }
        $this->em->remove($user);
        $this->em->flush();
        $response['status'] = true;
        $response['message'] = "User deleted successfully";
        return $response;
    }
}
