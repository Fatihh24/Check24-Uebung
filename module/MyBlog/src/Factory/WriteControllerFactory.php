<?php
namespace MyBlog\Factory;

use MyBlog\Controller\WriteController;
use MyBlog\Form\PostForm;
use MyBlog\Model\PostCommandInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MyBlog\Model\PostRepositoryInterface;

class WriteControllerFactory implements FactoryInterface
{
 
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');

        return new WriteController(
            $container->get(PostCommandInterface::class),
            $formManager->get(PostForm::class),
            $container->get(PostRepositoryInterface::class)
        );
    }
}