<?php

namespace MyBlog\Model;

class PostRepository implements PostRepositoryInterface
{
    public function findAllPosts()
    {
        return array_map(function ($post) {
            return new Post(
                $post['title'],
                $post['text'],
                $post['id']
            );
        }, $this->data);
    }

    public function findPost($id)
    {
        if (! isset($this->data[$id])) {
            throw new DomainException(sprintf('Artikel mit der Id "%s" konnte nicht gefunde werden', $id));
        }

        return new Post(
            $this->data[$id]['title'],
            $this->data[$id]['text'],
            $this->data[$id]['id']
        );
    }
}