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

        $blogPosts = $this->getDoctrine()
            ->getRepository( 'AppBundle:BlogPost' )
            ->findAll();
        $blogPosts = array_reverse( $blogPosts );
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/index.html.twig', array(
            'blogPosts' => $blogPosts,
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/add-blog-post", name="add_blog_post")
     */
    public function addBlogPostAction( Request $request ){

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
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
        $deletionObject = $em->getRepository('AppBundle:BlogPost')
            ->find($deletionId);
        if( $deletionObject ){
            $em->remove($deletionObject);
            $em->flush();
        }
        



        return $this->render('default/delete-blog-post.html.twig' );

    }

    /**
     * @Route("/edit-blog-post", name="edit_blog_post")
     */
    public function editBlogPostAction( Request $request ){
        $newPost = new BlogPost();
        $editId = $request->query->get('postId');

        $em = $this->getDoctrine()->getManager();
        $editObject = $em->getRepository('AppBundle:BlogPost')
            ->find($editId);
        

        
        $form = $this->createFormBuilder( $newPost )
            ->add( 'title', TextType::class, array( 'data' => $editObject->getTitle() ) )
            ->add( 'user', TextType::class, array( 'data' => $editObject->getUser() ) )
            ->add( 'message', TextareaType::class, array( 'data' => $editObject->getMessage() ) )
            ->add( 'save', SubmitType::class, array( 'label' => 'Compelete edit!' ) )
            ->getForm();

        $form->handleRequest($request);

        if( $form->isSubmitted()  && $form->isValid() ){
            //$editObject = $form->getData();
            $editObject->setMessage( $form->getData()->getMessage() );
            $editObject->setUser( $form->getData()->getUser() );
            $editObject->setTitle( $form->getData()->getTitle() );
            

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('homepage');
        }



        return $this->render('default/edit-blog-post.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}