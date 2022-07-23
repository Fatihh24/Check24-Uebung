<?php

namespace MyBlog\Model;

interface PostRepositoryInterface
{
    public function findAllPosts();

    public function findPost($id);
}