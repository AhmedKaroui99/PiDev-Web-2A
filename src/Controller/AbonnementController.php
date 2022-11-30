<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Associe;
use App\Entity\Promotion;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{
    /**
     * @Route("/abonnement", name="abonnement")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine();
        $tab = $em->getRepository(Abonnement::class)->findAll();
        $associe = $em->getRepository(Associe::class)->findAll();
        $promo = $em->getRepository(Promotion::class)->findAll();
        return $this->render('abonnement/index.html.twig', [
            'controller_name' => 'AbonnementController',
            'abonnements' => $tab,
            'associes' => $associe,
            'promos' => $promo,
        ]);
    }

    /**
     * @Route("/abon5262azd1", name="searchAbonnement")
     */
    public function search(Request $request): Response
    {
        $em = $this->getDoctrine();
        $tab = $em->getRepository(Abonnement::class)->search($request->get('search'));
        $associe = $em->getRepository(Associe::class)->findAll();
        $promo = $em->getRepository(Promotion::class)->findAll();
        return $this->render('abonnement/index.html.twig', [
            'controller_name' => 'AbonnementController',
            'abonnements' => $tab,
            'associes' => $associe,
            'promos' => $promo,
        ]);
    }

    /**
     * @Route("/abonnement/ajouter", name="ajouterAbonnement")
     */
    public function ajouter(Request $request, MailerInterface $mailer): Response
    {
        if($request->isMethod("post")) {
            $em = $this->getDoctrine()->getManager();

            $cat = new Abonnement();
            $a = $em->getRepository(Associe::class)->find($request->get('associe'));
            $p = $em->getRepository(Promotion::class)->find($request->get('promo'));
            $cat->setIdAssocie($a->getId());
            $cat->setIdPromo($p->getId());
            $cat->setName($request->get('name'));
            $cat->setType($request->get('type'));
            $cat->setPrice($request->get('price'));
            $cat->setRating($request->get('rating'));
            $cat->setNrating(0);

            $em->persist($cat);
            $em->flush();

            $email = (new Email())
                ->from('Admin@gmail.com')
                ->to('Admin@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Abonnement Ajouter!')
                ->text('Abonnement Ajouter')
                ->html('<p>Abonnement Ajouter</p>');

            $mailer->send($email);

            return $this->redirectToRoute('abonnement');
        }

        $em = $this->getDoctrine();
        $associe = $em->getRepository(Associe::class)->findAll();
        $promo = $em->getRepository(Promotion::class)->findAll();

        return $this->render('abonnement/ajouter.html.twig', [
            'associe' => $associe,
            'promo' => $promo,
        ]);
    }

    /**
     * @Route("/abonnement/modifier/{id}", name="modifierAbonnement")
     */
    public function modifier($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $abon = $em->getRepository(Abonnement::class)->find($id);

        if($request->isMethod("post")) {

            $a = $em->getRepository(Associe::class)->find($request->get('associe'));
            $p = $em->getRepository(Promotion::class)->find($request->get('promo'));
            $abon->setIdAssocie($a->getId());
            $abon->setIdPromo($p->getId());
            $abon->setName($request->get('name'));
            $abon->setType($request->get('type'));
            $abon->setPrice($request->get('price'));
            $abon->setRating($request->get('rating'));
            $abon->setNrating(0);

            $em->persist($abon);
            $em->flush();

            return $this->redirectToRoute('abonnement');
        }

        $em = $this->getDoctrine();
        $associe = $em->getRepository(Associe::class)->findAll();
        $promo = $em->getRepository(Promotion::class)->findAll();

        return $this->render('abonnement/modifier.html.twig', [
            'associe' => $associe,
            'promo' => $promo,
            'abon' => $abon,
        ]);
    }

    /**
     * @Route("/abonnement/supprimer/{id}", name="supprimerAbonnement")
     */
    public function supprimer($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Abonnement::class)->find($id);

        $em->remove($cat);
        $em->flush();

        return $this->redirectToRoute('abonnement');
    }

    /**
     * @Route("/abonnement/pdf", name="pdfAbonnement")
     */
    public function pdf(): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository(Abonnement::class)->findAll();
        $associe = $em->getRepository(Associe::class)->findAll();
        $promo = $em->getRepository(Promotion::class)->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('abonnement/pdf.html.twig', [
            'abonnements' => $tab,
            'associes' => $associe,
            'promos' => $promo,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
}
