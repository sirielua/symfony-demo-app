<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Repository\Blog\PostRepository;
use App\Repository\UserRepository;
use App\Pagination\PostPaginator;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Blog\Post;
use App\Form\Blog\PostType;
use App\Infrastructure\Type\Flash;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request, PostPaginator $paginator, UserRepository $users): Response
    {
        $pagination = $paginator->paginate(
            page: $request->query->get('page', 1),
            limit: null,
            options: $this->filterPaginationOptions($request, $paginator, $users)
        );
        
        return $this->render('blog/blog.html.twig', ['pagination' => $pagination]);
    }
    
    #[Route('/my-blog', name: 'app_blog_my')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function myBlog(Request $request, PostPaginator $paginator, UserRepository $users): Response
    {
        $pagination = $paginator->paginate(
            page: $request->query->get('page', 1),
            limit: 5,
            options: ['author' => $this->getUser()]
        );
        
        return $this->render('blog/my-blog.html.twig', ['pagination' => $pagination]);
    }
    
    #[Route('/blog/create', name: 'app_blog_create')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {        
        $post = new Post();
        $post->setAuthor($this->getUser());
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();
            
            $this->addFlash((Flash::SUCCESS)->value, 'Post succesfully created!');
            return $this->redirectToRoute('app_blog_view', ['slug' => $post->getSlug()]);
        }
        
        return $this->renderForm('blog/create.html.twig', ['form' => $form]);
    }
    
    #[Route('/blog/{slug}/update', name: 'app_blog_update')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function update(string $slug, Request $request, PostRepository $posts, EntityManagerInterface $em): Response
    {        
        $post = $posts->findOneBy(['slug' => $slug]);
        
        if (null == $post) {
            throw new NotFoundHttpException('Post not found');
        }
        
        if ($post->getAuthor() !== $this->getUser()) {
            throw new AccessDeniedHttpException('Access Denied');
        }
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            $this->addFlash((Flash::SUCCESS)->value, 'Post succesfully edited!');
            return $this->redirectToRoute('app_blog_view', ['slug' => $post->getSlug()]);
        }
        
        return $this->renderForm('blog/update.html.twig', ['form' => $form]);
    }
    
    #[Route('/blog/{slug}/delete', name: 'app_blog_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function delete(string $slug, PostRepository $posts, EntityManagerInterface $em): Response
    {           
        $post = $posts->findOneBy(['slug' => $slug]);
        
        if (null == $post) {
            throw new NotFoundHttpException('Post not found');
        }
        
        if ($post->getAuthor() !== $this->getUser()) {
            throw new AccessDeniedHttpException('Access Denied');
        }
        
        $em->remove($post);
        $em->flush();
        
        $this->addFlash((Flash::SUCCESS)->value, 'Post succesfully deleted!');
        return $this->redirectToRoute('app_blog_my');
    }
    
    #[Route('/blog/{slug}', name: 'app_blog_view')]
    public function view(string $slug, PostRepository $posts): Response
    {
        $post = $posts->findOneBy([
            'slug' => $slug,
        ]);
        
        if (null == $post) {
            throw new NotFoundHttpException('Post not found');
        }
        
        if (!$post->getIsPublished() && ($post->getAuthor() !== $this->getUser())) {
            throw new AccessDeniedHttpException('Access Denied');
        }

        return $this->render('blog/view.html.twig', ['post' => $post]);
    }
    
    private function filterPaginationOptions(Request $request, PostPaginator $paginator, UserRepository $users)
    {
        $options = ['isPublished' => true];
        
        $authorId = $request->query->get('author');
        if (null !== $authorId) {
            $author = $users->findBy(['id' => $authorId, 'isVerified' => true]);
            
            if (null !== $author) {
                $options['author'] = $author;
            } else {
                throw new NotFoundHttpException('User not found');
            }
            
            if ($this->getUser() && ($this->getUser() === $author)) {
                $options['isPublished'] = null;
            }
        }
        
        return $options;
    }
}
