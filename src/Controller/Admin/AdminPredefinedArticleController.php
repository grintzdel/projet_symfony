<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\PredefinedArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\PredefinedArticleType;

#[Route('/admin/predefined-article')]
class AdminPredefinedArticleController extends AbstractController
{
    #[Route('/', name: 'admin_predefined_articles_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $predefinedArticles = $entityManager->getRepository(PredefinedArticle::class)->findAll();

        return $this->render('admin/predefined_articles/index.html.twig', [
            'predefinedArticles' => $predefinedArticles,
        ]);
    }

    #[Route('/predefine', name: 'admin_predefine_article_no_id', methods: ['GET', 'POST'])]
    public function predefineArticle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $articleId = $request->request->get('id');

        if (!$articleId) {
            return $this->redirectToRoute('admin_predefined_articles_index');
        }

        $article = $entityManager->getRepository(Articles::class)->find($articleId);

        if (!$article) {
            throw $this->createNotFoundException();
        }

        $predefinedArticle = new PredefinedArticle();
        $predefinedArticle->setArticle($article);

        $entityManager->persist($predefinedArticle);
        $entityManager->flush();

        return $this->redirectToRoute('admin_predefined_articles_index');
    }

    #[Route('/new', name: 'admin_predefined_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $predefinedArticle = new PredefinedArticle();
        $form = $this->createForm(PredefinedArticleType::class, $predefinedArticle);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($predefinedArticle);
            $entityManager->flush();

            return $this->redirectToRoute('admin_predefined_articles_index');
        }

        return $this->render('admin/predefined_articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

