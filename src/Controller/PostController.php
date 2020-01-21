<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException('Ha ocurrido un error');
                }

                $post->setPhoto($newFilename);
            }
            $user=$this->getUser();
            $post->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('exito', Post::CREATED_POST);
            return $this->redirectToRoute('post');
        }
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'form' => $form -> createView()
        ]);
    }

    /**
     * @Route("/post/{id}", name="postDetail")
     */
    public function detailPost($id)
    {
        $em=$this->getDoctrine()->getManager();
        $post=$em->getRepository(Post::class)->find($id);
        return $this->render('post/detail.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/posts/user/", name="postUsers")
     */
    public function getpostUser()
    {
        $user = $this->getUser();
        $em=$this->getDoctrine()->getManager();
        $posts=$em->getRepository(Post::class)->findBy(['user' => $user]);
        return $this->render('post/user_posts.html.twig', [
            'posts' => $posts
        ]);
    }

        /**
     * @Route("/like", name="likePost", options={"expose"=true})
     */
    public function like(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $em=$this->getDoctrine()->getManager();
            $user=$this->getUser();
            $id = $request->request->get('id');
            $post = $em->getRepository(Post::class)->find($id);
            $likes = $post->getLikes();
            $likes .= $user->getId().',';
            $post->setLikes($likes);
            $em->flush();
            return new JsonResponse(['likes' => $likes]);
        }else{
            throw new Exception('error');
        }
    }
}