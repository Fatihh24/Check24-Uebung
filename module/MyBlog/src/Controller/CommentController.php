<?php

namespace MyBlog\Controller;

use MyBlog\Model\CommentTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MyBlog\Form\CommentForm;
use MyBlog\Model\Comment;

class CommentController extends AbstractActionController
{
    private $table;

    public function __construct(CommentTable $table)
    {
        $this->table = $table;
    }

    public function commentAction()
    {
        return new ViewModel([
            'comments' => $this->table->fetchAll(),
        ]);

        $id = $this->params()->fromRoute('id');

        try {
            $post = $this->postRepository->findPost($id);
        } catch (\InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('blog');
        }
    
        return new ViewModel([
            'post' => $post,
        ]);
    }

    public function addCommentAction()
    {
        $form = new CommentForm();
        $form->get('submit')->setValue('Hinzufügen');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $comment = new Comment();
        $form->setInputFilter($comment->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $comment->exchangeArray($form->getData());
        $this->table->saveComment($comment);
        return $this->redirect()->toRoute('comments');
    }
}