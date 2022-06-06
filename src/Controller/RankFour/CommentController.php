<?php

namespace App\Controller\RankFour;

use App\Entity\Comments;
use App\Entity\Certifications;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/{certification}/new", name="comment_new", methods={"POST"})
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

        return $this->render('@user/comments/new_comment.html.twig', [
            'user' => $this->user,
            'comment' => $comment
        ]);
    }

    /**
    * @Route("/edit/form", name="comment_edit_form", methods={"POST"})
    */
    public function editFormComment(Request $request) {
        $data = $request->request->all();

        return $this->render('@user/comments/edit_form.html.twig', [
            'content' => $data["content"]
        ]);
    }

    /**
    * @Route("/{comment}/edit", name="comment_edit", methods={"POST"})
    */
    public function editComment(Comments $comment, Request $request) {

        $data = $request->request->all();

        if (!empty($data["content"]) && $comment->getCreatedBy() == $this->user) {
            $comment->setContent($data["content"]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->render('@user/comments/one_comment.html.twig', [
            'user' => $this->user,
            'comment' => $comment
        ]);
    }

    /**
    * @Route("/{comment}/delete", name="comment_delete", methods={"POST"})
    */
    public function deleteComment(Comments $comment) : Response {

        if ($comment->getCreatedBy() == $this->user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return new Response();
    }

    /**
    * @Route("/reply/form", name="reply_form", methods={"POST"})
    */
    public function replyForm() : Response {
        return $this->render('@user/comments/reply_form.html.twig', [
            'user' => $this->user
        ]);
    }

    /**
     * @Route("/reply/{comment}/new", name="reply_add", methods={"POST"})
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

        return $this->render('@user/comments/new_comment.html.twig', [
            'user' => $this->user,
            'comment' => $reply
        ]);
    }

     /**
        * @Route("/reply/{comment}", name="get_replies", methods={"POST"})
    */
    public function replySection(Comments $comment) : Response {
        return $this->render('@user/comments/reply_section.html.twig', [
            'comment' => $comment
        ]);
    }
}