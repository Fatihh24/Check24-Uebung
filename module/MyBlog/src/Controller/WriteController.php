<?php

namespace MyBlog\Controller;

use MyBlog\Form\PostForm;
use MyBlog\Model\Post;
use MyBlog\Model\PostCommandInterface;
use MyBlog\Model\PostRepositoryInterface;
use InvalidArgumentException;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class WriteController extends AbstractActionController
{
    private $command;
    private $form;
    private $repository;

    public function __construct(
        PostCommandInterface $command,
        PostForm $form,
        PostRepositoryInterface $repository
    ) {
        $this->command = $command;
        $this->form = $form;
        $this->repository = $repository;
    }

    public function addAction()
    {
        $request   = $this->getRequest();
        $viewModel = new ViewModel(['form' => $this->form]);

        if (! $request->isPost()) {
            return $viewModel;
        }

        $this->form->setData($request->getPost());

        if (! $this->form->isValid()) {
            return $viewModel;
        }

        $post = $this->form->getData();

        try {
            $post = $this->command->insertPost($post);
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $this->redirect()->toRoute(
            'myblog/detail',
            ['id' => $post->getId()]
        );
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if (! $id) {
            return $this->redirect()->toRoute('myblog');
        }

        try {
            $post = $this->repository->findPost($id);
        } catch (InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('myblog');
        }

        $this->form->bind($post);
        $viewModel = new ViewModel(['form' => $this->form]);

        $request = $this->getRequest();
        if (! $request->isPost()) {
            return $viewModel;
        }

        $this->form->setData($request->getPost());

        if (! $this->form->isValid()) {
            return $viewModel;
        }

        $post = $this->command->updatePost($post);
        return $this->redirect()->toRoute(
            'myblog/detail',
            ['id' => $post->getId()]
        );
    }
}