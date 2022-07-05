<?php

namespace App\Controller\RankThree;

use App\Entity\Comment;
use App\Entity\Exam;

use App\Repository\CommentsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/your/comments")
 */
class CommentAPIController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/add/{id}", name="comment_new", methods={"GET", "POST"})
    */
    public function newComment(Exam $exam, Request $request) {
        $content = $request->request->get("content");

        if (!empty($content)) {
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setCreatedBy($this->user);
            $comment->setWrittenOn($exam);
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->render('@components/comments/new_comment.html.twig', [
                'user' => $this->user,
                'comment' => $comment
            ]);
        }

        return new Response();
    }

    /**
    * @Route("/edit/form", name="comment_edit_form", methods={"POST"})
    */
    public function editFormComment(Request $request) {
        $content = $request->request->get("content");

        return $this->render('@components/comments/edit_form.html.twig', [
            'content' => $content
        ]);
    }

    /**
    * @Route("/edit", name="comment_edit_path", methods={"POST"})
    */
    public function editComment(CommentsRepository $commentsRepository, Request $request) {

        $comment_id =$request->request->getInt("comment_id");
        $comment = $commentsRepository->find($comment_id);

        $content = $request->request->get("content");

        if (!empty($content) && $comment->getCreatedBy() == $this->user) {
            $comment->setContent($content);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->render('@components/comments/one_comment.html.twig', [
            'user' => $this->user,
            'comment' => $comment
        ]);
    }

    /**
    * @Route("/delete", name="comment_delete_path", methods={"POST"})
    */
    public function deleteComment(CommentsRepository $commentsRepository, Request $request) {

        $comment_id = $request->request->getInt("comment_id");
        $comment = $commentsRepository->find($comment_id);

        if ($comment->getCreatedBy() == $this->user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return new Response();
    }
}