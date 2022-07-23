<?php

namespace Blog\Model;

class Blog {
    public $id;
    public $title;
    public $text;

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->text = !empty($data['text']) ? $data['text'] : null;
    }
}