<?php

namespace App\Controller;

use App\Entity\Associe;
use App\Entity\Client;
use App\Entity\Users;
use App\Form\ProfileType;
use App\Repository\ClientRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UsersController extends AbstractController
{
    private $logger;
    /**
     * UsersController constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }

    /**
     * @Route("/users/signup", name="users", methods={"POST"})
     */
    public function signup(Request $request, UsersRepository $usersRepository): Response
    {
        try {
            $this->logger->info('----------------------------------------------------------');
            $data = json_decode($request->getContent());
            $this->logger->info($request->request->get('email'));
            $this->logger->info('----------------------////////////////////////////////////-------------------');

            $user = new Users();
            $user->setEmail($request->request->get('email'));
            $user->setPassword($request->request->get('password'));
            $user->setRole($request->request->get('role'));
            $user->setIsValid(true);
            $user->setIsDisabled(false);
            $fieldsVerifications = '';
            if (!filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $fieldsVerifications .= "Invalid email format ,";
            }
            if($request->request->get('password') != $request->request->get('confirmPassword')) {
                $fieldsVerifications .= "Invalid password and confirm password";
            }
            if (strlen($fieldsVerifications) > 0 ) {
                return $this->json([
                    'user' => null,
                    'type' => 'error',
                    'message' => $fieldsVerifications
                ]);
            }

            if($usersRepository->findOneByEmail($request->request->get('email')) != null) {
                return $this->json([
                    'user' => null,
                    'type' => 'error',
                    'message' => 'email already exists'
                ]);
            }
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $user = $usersRepository->findOneByEmail($request->request->get('email'));
            if($request->request->get('role') == 'client') {
                $client = new Client();
                $client->setUserId($user->getId());
                $em->persist($client);
                $em->flush();
            } else if ($request->request->get('role') == 'associe') {
                $associe = new Associe();
                $associe->setUserId($user->getId());
                $em->persist($associe);
                $em->flush();
            }
            return $this->json([
                'user' => $user
            ]);

        } catch (Exception $e) {
            return $this->json([
                'user' => null,
                'error' => $e
            ]);
        }
    }

    /**
     * @Route("/users/login", name="usersLogin", methods={"POST"})
     */
    public function login(Request $request, UsersRepository $usersRepository): Response
    {
        try {
            $this->logger->info('----------------------------------------------------------');
//            $data = json_decode($request->getContent(),true);
//            $this->logger->info($data['email']);
            $user = $usersRepository->findOneByEmail($request->request->get('email'));
            if($user == null) {
                return $this->json([
                    'user' => null,
                    'type' => 'error',
                    'message' => 'user does not exist'
                ]);
            }
            if($user->getPassword() != $request->request->get('password')) {
                return $this->json([
                    'user' => null,
                    'type' => 'error',
                    'message' => 'wrong password'
                ]);
            }
            $_SESSION['user'] = $user;
            return $this->json([
                'user' => $user
            ]);
        } catch (Exception $e) {
            return $this->json([
                'user' => null
            ]);
        }
    }

    /**
     * @Route("/profile", name="usersProfile")
     */
    public function profile(): Response
    {
//        $user = $usersRepository->findClientByUserIdPopulated($id);
//        $this->logger->info('fzefzefzefzefz');

        return $this->render('users/profile.html.twig');
    }

    /**
     * @Route("/profile/client", name="clientProfile", methods={"POST"})
     */
    public function clientProfile(ClientRepository $clientRepository, Request $request): Response
    {
//        $user = $usersRepository->findClientByUserIdPopulated($id);
//        $this->logger->info('fzefzefzefzefz');
        return $this->json([
            'client' => $clientRepository->findOneByUserId($request->request->get('id'))
        ]);
    }
//
    /**
     * @Route ("profile/update/{id}",name = "updateClientProfile")
     */
    function update (ClientRepository $repository, $id,Request $request) {
        $client = $repository->find($id);
        $form = $this->createForm(ProfileType::class,$client);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("usersProfile");
        }

        return $this->render('users/update.html.twig',[
            'f'=>$form->createView()
        ]);
    }

    /**
     * @Route("/profile/client/disableAccount", name="clientDisable", methods={"POST"})
     */
    public function disableAccount(UsersRepository $usersRepository, Request $request): Response
    {
//        $user = $usersRepository->findClientByUserIdPopulated($id);
//        $this->logger->info('fzefzefzefzefz');
        return $this->json([
            'client' => $usersRepository->disableAccount($request->request->get('id'), true)
        ]);
    }

//    /**
//     * @Route("/profile/client/getAll", name="clientProfile", methods={"GET"})
//     */
//    public function getAllClient(UsersRepository $usersRepository, Request $request): Response
//    {
////        $user = $usersRepository->findClientByUserIdPopulated($id);
//        $this->logger->info('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
//        return $this->json([
//            'client' => $usersRepository->findClientByUserIdPopulated()
//        ]);
//    }

    /**
     * @Route("/profile/client/getAll", name="getAllUsers", methods={"GET"})
     */
    public function getAllUsers(UsersRepository $usersRepository, Request $request): Response
    {
//        $user = $usersRepository->findClientByUserIdPopulated($id);
        $this->logger->info('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        return $this->json([
            'client' => $usersRepository->getAllUsers()
        ]);
    }

//    /**
//     * @Route("/delete/{id}", name="delete")
//     */
//    public function deleteClient($id): Response
//    {
//        $client = $this->getDoctrine()->getRepository(Client::class)->find($id);
//        $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
//
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($user);
//        $em->flush();
//        return $this->redirectToRoute('users');
//    }
}
