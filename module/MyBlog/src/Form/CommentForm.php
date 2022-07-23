<?php

namespace MyBlog\Form;

use Laminas\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comments');

        $this->add([
            'name' => 'comment_id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'comment',
            'type' => 'text',
            'options' => [
                'label' => 'Kommentar',
            ],
        ]);
        $this->add([
            'name' => 'article_id',
            'type' => 'text',
            'options' => [
                'label' => 'Artikel ID',
            ],
        ]);
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}