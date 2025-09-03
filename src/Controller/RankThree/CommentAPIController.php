<?php

namespace App\Controller\RankThree;

use App\Entity\Comment;
use App\Entity\Exam;

use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/your/comments")
 */
class CommentAPIController extends AbstractController {

    const MAX_COMMENTS_CHECK = 2;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/check/{exam_id}/{user_id}", name="comment_surplus_check", methods={"GET"})
    */
    public function surplusMax(ManagerRegistry $doctrine, $exam_id, $user_id) {
        $checkNumber = $doctrine->getRepository(Comment::class)->check($user_id, $exam_id);

        return new Response(($checkNumber >= self::MAX_COMMENTS_CHECK));
    }

    /**
    * @Route("/add/{id}", name="comment_new", methods={"GET", "POST"})
    */
    public function newComment(Exam $exam, Request $request, ManagerRegistry $doctrine) {
        $content = $request->request->get("content");

        if (!empty($content)) {
            if ($this->checkLinks($content) == false) {
                $checkSurpass = $this->surplusMax($doctrine, $exam->getId(), $this->user->getId())->getContent();
                if ($checkSurpass == false) {
                    $comment = new Comment();
                    $comment->setContent($content)
                            ->setCreatedBy($this->user)
                            ->setWrittenOn($exam);

                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($comment);
                    $entityManager->flush();
        
                    return $this->render('@components/comments/new_comment.html.twig', [
                        'user' => $this->user,
                        'comment' => $comment
                    ]);
                }

                return new Response("You cannot post more than 2 comments inside an exam.", 500);
            }

            return new Response("Comment cannot contain links.", 500);
        }

        return new Response("Comment cannot be empty.", 500);
    }

    /**
    * @Route("/edit/form", name="comment_edit_form", methods={"POST"})
    */
    public function editFormComment(Request $request) {
        $content = $request->request->get("content");
        $comment_id = $request->request->get("comment_id");

        return $this->render('@components/comments/edit_form.html.twig', [
            'content' => $content,
            'comment_id' => $comment_id
        ]);
    }

    /**
    * @Route("/edit", name="comment_edit_path", methods={"POST"})
    */
    public function editComment(CommentsRepository $commentsRepository, Request $request, EntityManagerInterface $entityManager) {

        $comment_id = $request->request->getInt("comment_id");
        $comment = $commentsRepository->find($comment_id);
        $content = $request->request->get("content");

        if (!empty($content)) {
            if ($comment->getContent() != $content) {
                if ($comment->getCreatedBy() == $this->user) {
                    if ($this->checkLinks($content) == false) {
                        $comment->setContent($content)
                                ->setIsEdited(true);

                        $entityManager->persist($comment);
                        $entityManager->flush();

                    } else {
                        return new Response("Comment cannot contain links.", 500);
                    }
                } else {
                    return new Response("What the hell are you doing anyway?", 500);
                }
            }

            return $this->render('@components/comments/one_comment.html.twig', [
                'user' => $this->user,
                'comment' => $comment
            ]);

        } else {
            return new Response("Comment cannot be empty.", 500);
        }
    }

    /**
    * @Route("/delete", name="comment_delete_path", methods={"POST"})
    */
    public function deleteComment(CommentsRepository $commentsRepository, Request $request, EntityManagerInterface $entityManager) {

        $comment_id = $request->request->getInt("comment_id");
        $comment = $commentsRepository->find($comment_id);

        if ($comment->getCreatedBy() == $this->user) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return new Response();
    }

    public function checkLinks($content) {
        $regex = "((http|https|ftp|ftps)\:\/\/)|([a-zA-Z0-9\-\.]+\.)[a-zA-Z]{2,3}(\/\S*)?";

        if (preg_match("/$regex/", $content)) {
            return true;
        }

        return false;
    }
}