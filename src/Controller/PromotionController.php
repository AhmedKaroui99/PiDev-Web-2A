<?php

namespace App\Controller;

use App\Entity\Promotion;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
{
    /**
     * @Route("/promotion", name="promotion")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository(Promotion::class)->findAll();
        return $this->render('Promotion/index.html.twig', [
            'promotion' => $tab,
        ]);
    }

    /**
     * @Route("/promoad5ad2s1", name="SearchPromotion")
     */
    public function search(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository(Promotion::class)->search($request->get('search'));
        return $this->render('Promotion/index.html.twig', [
            'promotion' => $tab,
        ]);
    }

    /**
     * @Route("/promotion/ajouter", name="ajouterPromotion")
     */
    public function ajouter(Request $request, MailerInterface $mailer): Response
    {
        if($request->isMethod("post")) {
            $em = $this->getDoctrine()->getManager();

            $cat = new Promotion();
            $cat->setName($request->get('name'));
            $cat->setValue($request->get('value'));
            $cat->setDate(new \DateTime($request->get('date')));
            $cat->setNdays($request->get('ndays'));

            $em->persist($cat);
            $em->flush();

            $email = (new Email())
                ->from('Admin@gmail.com')
                ->to('Admin@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Promotion Ajouter!')
                ->text('Promotion Ajouter')
                ->html('<p>Promotion Ajouter</p>');

            $mailer->send($email);

            return $this->redirectToRoute('promotion');
        }
        return $this->render('promotion/ajouter.html.twig', [
        ]);
    }

    /**
     * @Route("/promotion/modifier/{id}", name="modifierPromotion")
     */
    public function modfier($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Promotion::class)->find($id);

        if($request->isMethod("post")) {
            $em = $this->getDoctrine()->getManager();

            $cat->setName($request->get('name'));
            $cat->setValue($request->get('value'));
            $cat->setDate(new \DateTime($request->get('date')));
            $cat->setNdays($request->get('ndays'));

            $em->persist($cat);
            $em->flush();

            return $this->redirectToRoute('promotion');
        }
        return $this->render('promotion/modifier.html.twig', [
            'promotion' => $cat,
        ]);
    }

    /**
     * @Route("/promotion/supprimer/{id}", name="supprimerPromotion")
     */
    public function supprimer($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Promotion::class)->find($id);

        $em->remove($cat);
        $em->flush();

        return $this->redirectToRoute('promotion');
    }

    /**
     * @Route("/promotion/pdf", name="pdfPromotion")
     */
    public function pdf(): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository(Promotion::class)->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('promotion/pdf.html.twig', [
            'promotion' => $tab,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
}
