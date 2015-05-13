<?php

namespace News\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

 class NewsTable
 {
     /**
      * @var TableGateway
      */
     protected $tableGateway;

     /**
      * @param \Zend\Db\TableGateway\TableGateway $tableGateway
      */
     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     /**
      * Returns all News in db
      * @return \Zend\Db\ResultSet\ResultSet
      */
     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select(function (Select $select) {
             $select->order('date_posted DESC');
         });
         return $resultSet;
     }

     /**
      * Get the news with the corresponding id
      * @param int|string $id
      *
      * @return array|\ArrayObject|null
      * @throws \Exception
      */
     public function getNews($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(['id' => $id]);
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     /**
      * Saves the given news object to the db
      * @param \News\Model\News $news
      *
      * @throws \Exception
      */
     public function saveNews(News $news)
     {
         $data = [
             'author' => $news->getAuthor(),
             'title'  => $news->getTitle(),
             'content' => $news->getContent(),
             'date_posted' => $news->getDatePosted(),
         ];

         $id = (int) $news->getId();
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getNews($id)) {
                 $this->tableGateway->update($data, ['id' => $id]);
             } else {
                 throw new \Exception('News id does not exist');
             }
         }
     }

     /**
      * Deletes the news with the corresponding id.
      * @param int|string $id
      */
     public function deleteNews($id)
     {
         $this->tableGateway->delete(['id' => (int) $id]);
     }
 }
