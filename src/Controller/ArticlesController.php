<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Form\AvisType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'app_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thumbnailFile = $form['thumbnail']->getData();

            if ($thumbnailFile) {
                $originalFilename = pathinfo($thumbnailFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$thumbnailFile->guessExtension();

                try {
                    $thumbnailFile->move(
                        $this->getParameter('thumbnails_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // On peut gérer l'exception ici si y'a un problème lors de l'upload
                }

                $article->setThumbnail($newFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_articles_show', methods: ['GET', 'POST'])]
    public function show(Articles $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setIdUser($this->getUser());
            $avis->setIdArticles($article);
            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_show', ['id' => $article->getId()]);
        }

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'avis' => $article->getAvis(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thumbnailFile = $form['thumbnail']->getData();

            if ($thumbnailFile) {
                $originalFilename = pathinfo($thumbnailFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$thumbnailFile->guessExtension();

                try {
                    $thumbnailFile->move(
                        $this->getParameter('thumbnails_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer les erreurs là aussi
                }

                $article->setThumbnail($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/ajouter-avis', name: 'articles_ajouter_avis', methods: ['GET', 'POST'])]
    public function ajouterAvis(Request $request, Articles $article): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setIdUser($this->getUser());
            $avis->setIdArticles($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_show', ['id' => $article->getId()]);
        }

        return $this->render('articles/ajouter_avis.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/save', name: 'app_articles_save', methods: ['POST'])]
    public function save(Articles $article, EntityManagerInterface $entityManager): Response
    {
        $this->getUser()->addSavedArticle($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_articles_show', ['id' => $article->getId()]);
    }
}