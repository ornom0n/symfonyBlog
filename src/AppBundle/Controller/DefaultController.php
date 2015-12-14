<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\BlogPost;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // $blogPost = new BlogPost();
        // $blogPost->setTitle('HOT NEW SUSHIBAR');
        // $blogPost->setUser('Bob Bobson');
        // $blogPost->setMessage('I LOVE SUSHEY');

        // $blogPost->setDate( new \DateTime('now') );

        // $em = $this->getDoctrine()->getManager();

        // $em->persist($blogPost);
        // $em->flush();

        // replace this example code with whatever you need

        $blogRepository = $this->getDoctrine()
            ->getRepository( 'AppBundle:BlogPost' );

        $blogPosts = $blogRepository->findAll();

        return $this->render('default/index.html.twig', array(
            'blogPosts' => $blogPosts
        ));
    }

    /**
     * @Route("/add-blog-post", name="add_blog_post")
     */
    public function addBlogPostAction(Request $request){
        $newPost = new BlogPost();
        
        $form = $this->createFormBuilder($newPost)
            ->add( 'title', TextType::class )
            ->add( 'user', TextType::class )
            ->add( 'message', TextareaType::class )
            ->add( 'save', SubmitType::class, array( 'label' => 'Create post!' ) )
            ->getForm();

        $form->handleRequest($request);

        if( $form->isSubmitted()  && $form->isValid() ){
            $newPost = $form->getData();
            $newPost->setDate( new \DateTime('now') );

            $em = $this->getDoctrine()->getManager();
            $em->persist($newPost);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/add-blog-post.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
