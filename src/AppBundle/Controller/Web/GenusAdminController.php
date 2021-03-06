<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Genus;
use AppBundle\Form\GenusFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_MANAGE_GENUS')")
 * @Route("/admin")
 */
class GenusAdminController extends Controller
{
    /**
     * @Route("/genus/new", name="admin_genus_new")
     * @param Request $request
     * @return Response
     * @ Security("is_granted('ROLE_ADMIN')") fatto a livello di controller
     */
    public function newAction(Request $request)
    {
//        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
//            throw $this->createAccessDeniedException('GET OUT!');
//        }
//      shorter way:
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(GenusFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Genus created by %s!', $this->getUser()->getEmail())
            );

            return $this->redirectToRoute('app_genus_list');
        }

        return $this->render('admin/genus/new.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/genus/{id}/edit", name="admin_genus_edit")
     * @param Request $request
     * @param Genus $genus
     * @return Response
     */
    public function editAction(Request $request, Genus $genus)
    {
        $form = $this->createForm(GenusFormType::class, $genus);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();

            $this->addFlash('success', 'Genus updated!');

            return $this->redirectToRoute('app_genus_list');
        }

        return $this->render('admin/genus/edit.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/genus/{id}/delete", name="admin_genus_delete")
     * @param Genus $genus
     * @return Response
     */
    public function deleteAction(Genus $genus)
    {
        if (!$this->isGranted('GENUS_DELETE', $genus)) {
            throw $this->createAccessDeniedException('NO!');
        }

        $em = $this->getDoctrine()->getManager();
        $genusNotesRepository = $em->getRepository('AppBundle:GenusNote');
        $notes = $genusNotesRepository->findBy(['genus' => $genus]);
        foreach ($notes as $note) {
            $em->remove($note);
        }
        $em->remove($genus);
        $em->flush();
        $this->addFlash('success', 'Genus deleted!');
        return $this->redirectToRoute('app_genus_list');
    }
}
