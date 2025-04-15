<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Interfaces\AuthInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['POST'])]
    public function index(AuthInterface $authService): JsonResponse
    {
        $response ['data'] = $authService->list();
        return new JsonResponse($response);
    }

    #[Route('/register', name: 'app_user_new', methods: ['POST'])]
    public function register(Request $request, AuthInterface $authService, EntityManagerInterface $em): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $params['email']]);
            if (!empty($user)) {
                $response["message"] = 'This user already exists';
                return new JsonResponse($response);
            }
            $res = $authService->registerORUpdate($params);
            if ($res['status']){
                $response["message"] = 'User created';
            }
        } else {
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/update/email', name: 'app_user_update_by_email', methods: ['POST'])]
    public function editByEmail(Request $request, AuthInterface $authService, EntityManagerInterface $em): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $params['email']]);
            if (empty($user)) {
                $response["message"] = 'This user not found';
                return new JsonResponse($response);
            }
            $response = $authService->registerORUpdate($params);
            if ($response['status']){
                $response["message"] = 'User ' . $params['email'] . ' updated successfully';
            }
        } else {
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/update/id', name: 'app_user_update_by_Id', methods: ['POST'])]
    public function edit(Request $request, AuthInterface $authService, EntityManagerInterface $em): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $user = $em->getRepository(User::class)->findOneBy(['id' => $params['id']]);
            if (empty($user)) {
                $response["message"] = 'This user not found';
                return new JsonResponse($response);
            }
            $response = $authService->updateById($params);
        } else {
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/detail', name: 'app_user_show', methods: ['POST'])]
    public function show(Request $request,AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->detail($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/detail/id', name: 'app_user_show_by_id', methods: ['POST'])]
    public function showById(Request $request,AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->detailById($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/detail/alternate', name: 'app_user_show_alternate', methods: ['POST'])]
    public function showAlternate(Request $request,AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->detailAlternate($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(Request $request,AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->login($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/delete/email', name: 'app_user_delete_by_email', methods: ['POST'])]
    public function deleteByEmail(Request $request, AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->deleteByEmail($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }

    #[Route('/delete/id', name: 'app_user_delete_by_id', methods: ['POST'])]
    public function deleteById(Request $request, AuthInterface $authService): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        $response = [];
        if (!empty($params)) {
            $response = $authService->deleteById($params);
        }else{
            $response['message'] = 'params is empty';
        }
        return new JsonResponse($response);
    }
}
