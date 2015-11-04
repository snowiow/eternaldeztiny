<?php

namespace News\Model;

use News\Model\Comment;

class CommentTable
{

    /**
     * @var Tablegateway
     */
    private $tablegateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tablegateway = $tableGateway;
    }

    public function getComment(int $id)
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where(['comment.id' => $id]);
        });

        $row = $rowset->current();
        if (!$row) {
            throw new \Exception('Could not find row ' . $id);
        }
        return $row;
    }

    public function saveComment(Comment $comment)
    {
        $data = [
            'news_id'     => $comment->getId(),
            'account_id'  => $comment->getAccountId(),
            'content'     => $comment->getContent(),
            'date_posted' => $comment->getDatePosted(),
        ];

        $id = $comment->getid();
        if ($id == 0) {
            $this->tablegateway->insert($data);
        } else {
            if ($this->getComment($id)) {
                $this->tablegateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Comment id does not exist');
            }
        }
    }

}
