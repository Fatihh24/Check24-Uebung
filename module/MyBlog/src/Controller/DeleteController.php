<?php
namespace MyBlog\Controller;

use MyBlog\Model\Post;
use MyBlog\Model\PostCommandInterface;
use MyBlog\Model\PostRepositoryInterface;
use InvalidArgumentException;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DeleteController extends AbstractActionController
{
    private $command;
    private $repository;

    public function __construct(
        PostCommandInterface $command,
        PostRepositoryInterface $repository
    ) {
        $this->command = $command;
        $this->repository = $repository;
    }

    public function deleteAction()
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

        $request = $this->getRequest();
        if (! $request->isPost()) {
            return new ViewModel(['post' => $post]);
        }

        if ($id != $request->getPost('id')
            || 'Delete' !== $request->getPost('confirm', 'no')
        ) {
            return $this->redirect()->toRoute('myblog');
        }

        $post = $this->command->deletePost($post);
        return $this->redirect()->toRoute('myblog');
    }
}