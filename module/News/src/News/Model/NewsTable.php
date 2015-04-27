<?php

namespace News\Model;

 use Zend\Db\TableGateway\TableGateway;

 class NewsTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getNews($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveNews(News $news)
     {
         $data = array(
             'author' => $news->author,
             'title'  => $news->title,
             'content' => $news->content,
             'date_posted' => $news->date_posted,
         );

         $id = (int) $news->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getNews($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('News id does not exist');
             }
         }
     }

     public function deleteNews($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }
