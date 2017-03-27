<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class APIUsersController extends Controller
{
    /**
     * @Route("/api/users", name="api_users_new")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $user = new User();
        $form = $this->createForm(UserRegistrationForm::class, $user);
        $form->submit($data);
//        $user->setEmail($data['email']);
//        $user->setPlainPassword($data['plainPassword']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = new Response('It worked', Response::HTTP_CREATED);
        $url = $this->generateUrl(
            'api_users_show',
            ['email' => $user->getEmail()]
        );
        $response->headers->set('Location', $url);
        return $response;
    }

    /**
     * @Route("/api/users/{email}", name="api_users_show")
     * @Method("GET")
     * @return Response
     */
    public function showAction($email)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByEmail($email);

        if (!$user) {
            throw $this->createNotFoundException(sprintf(
                'No user found with email "%s"',
                $user
            ));
        }

        $json = json_encode([
            'email' => $user->getEmail()
        ]);

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/users/{email}", name="api_users_delete")
     * @Method("DELETE")
     * @return Response
     */
    public function deleteAction($email)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByEmail($email);

        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        $response = new Response('', Response::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * @Route("/api/users", name="api_users_list")
     * @Method("GET")
     * @return Response
     */
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $list = [];
        foreach ($users as $user) {
            $list[] = [
                'email' => $user->getEmail()
            ];
        }

        $response = new JsonResponse($list, Response::HTTP_OK);
        return $response;
    }
}
