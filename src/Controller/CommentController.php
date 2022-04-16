<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Certifications;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;

use App\Repository\CommentsRepository;
use App\Repository\CertificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/")
 */
class CommentController extends AbstractController {
    
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/comment/new/{certification}", name="comment_new", methods={"POST"})
    */ 
    public function newComment(Certifications $certification, Request $request) {
        $data = $request->request->all();

        $comment = new Comments();
        $comment->setContent($data["content"]);
        $comment->setCreatedBy($this->user);
        $comment->setWrittenOn($certification);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->render('comments/new_comment.html.twig', [
            'user' => $this->user,
            'comment' => $comment
        ]);
    }

    /**
    * @Route("/comment/edit", name="comment_edit_form", methods={"POST"})
    */ 
    public function editFormComment(Request $request) {
        $data = $request->request->all();

        return $this->render('comments/edit_form.html.twig', [
            'content' => $data["content"]
        ]);
    }

    /**
    * @Route("/comment/edit/{comment}", name="comment_edit", methods={"POST"})
    */ 
    public function editComment(Comments $comment, Request $request) {
        $data = $request->request->all();
        $comment->setContent($data["content"]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->render('comments/one_comment.html.twig', [
            'user' => $this->user,
            'comment' => $comment
        ]);
    }

    /**
    * @Route("/comment/delete/{comment}", name="comment_delete", methods={"POST"})
    */ 
    public function deleteComment(Comments $comment, Request $request) : Response {

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return new Response();
    }

    /**
    * @Route("/comment/reply/form", name="reply_form", methods={"POST"})
    */ 
    public function replyForm(Request $request) : Response {
        return $this->render('comments/reply_form.html.twig', [
            'user' => $this->user
        ]);
    }

    /**
     * @Route("/comment/reply/add/{comment}", name="reply_add", methods={"POST"})
    */ 
    public function addReply(Comments $comment, Request $request) : Response {
        $data = $request->request->all();
        $reply = new Comments();

        $reply->setContent($data["content"]);
        $reply->setCreatedBy($this->user);
        $reply->setReplyTo($comment);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($reply);
        $em->flush();

        return $this->render('comments/new_comment.html.twig', [
            'user' => $this->user,
            'comment' => $reply
        ]);
    }

     /**
        * @Route("/comment/reply/get/{comment}", name="get_replies", methods={"POST"})
    */ 
    public function replySection(Comments $comment) : Response {
        return $this->render('comments/reply_section.html.twig', [
            'comment' => $comment
        ]);
    }
}
