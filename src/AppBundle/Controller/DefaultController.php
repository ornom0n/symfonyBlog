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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $blogRepository = $this->getDoctrine()
            ->getRepository( 'AppBundle:BlogPost' );
        $blogPosts = $blogRepository->findAll();

        //$deleteButton->add( 'deletePost', ButtonType::class );

        return $this->render('default/index.html.twig', array(
            'blogPosts' => $blogPosts,
            'deleteButton' => 'I am delete'
        ));
    }

    /**
     * @Route("/add-blog-post", name="add_blog_post")
     */
    public function addBlogPostAction( Request $request ){
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

    /**
     * @Route("/delete-blog-post", name="delete_blog_post")
     */
    public function deleteBlogPostAction( Request $request ){
        $deletionId = $request->query->get('postId');

        $em = $this->getDoctrine()->getManager();
        $deletionObject = $em->getRepository('AppBundle:BlogPost')->find($deletionId);
        $em->remove($deletionObject);
        $em->flush();



        return $this->render('default/delete-blog-post.html.twig' );

    }

    /**
     * @Route("/edit-blog-post", name="edit_blog_post")
     */
    public function editBlogPostAction( Request $request ){
        $editId = $request->query->get('postId');

        $em = $this->getDoctrine()->getManager();
        $editObject = $em->getRepository('AppBundle:BlogPost')->find($editId);
        
        $defaults = array(
            'title' => $editObject->getTitle(),
            'user' => $editObject->getUser(),
            'message' => $editObject->getMessage(),
            );

        $newPost = new BlogPost();
        
        $form = $this->createFormBuilder($newPost, $defaults)
            ->add( 'title', TextType::class )
            ->add( 'user', TextType::class )
            ->add( 'message', TextareaType::class )
            ->add( 'save', SubmitType::class, array( 'label' => 'Compelete edit!' ) )
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



        return $this->render('default/delete-blog-post.html.twig' );

    }
}
