<?php

namespace News\Model;

class News 
{
    public $id;
    public $author;
    public $title;
    public $content;
    public $date_posted;

    public function exchangeArray($data) 
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->author = (!empty($data['author'])) ? $data['author'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->content = (!empty($data['content'])) ? $data['content'] : null;
        $this->date_posted = (!empty($data['date_posted'])) ? 
                                                $data['date_posted'] : null;

    }
}
