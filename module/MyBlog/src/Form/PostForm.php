<?php
namespace MyBlog\Form;

use Laminas\Form\Form;

class PostForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'post',
            'type' => PostFieldset::class,
        ]);

        $this->add([
            'name' => 'post',
            'type' => PostFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Insert new Post',
            ],
        ]);
    }
}