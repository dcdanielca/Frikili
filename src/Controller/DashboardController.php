<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->findAllPosts();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        $comments = $em->getRepository(Comment::class)->findCommentsDashboard($this->getUser()->getId());
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'Bienvenido a Dashboard',
            'pagination' => $pagination,
            'comments' => $comments
        ]);
    }
}
