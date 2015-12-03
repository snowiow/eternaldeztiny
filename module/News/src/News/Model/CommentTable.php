<?php

namespace News\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

use News\Model\Comment;

class CommentTable
{

    /**
     * @var Tablegateway
     */
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
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

    public function getCommentsByNewsId(int $newsId)
    {
        return $this->tableGateway->select(function (Select $select) use ($newsId) {
            $select
                ->join(['a' => 'account'], 'comment.account_id = a.id', ['name', 'avatar'])
                ->where(['news_id' => $newsId])
                ->order(['date_posted ASC']);
        });
    }

    public function getCommentByAccountId(int $accountId)
    {
        return $this->tableGateway->select(function (Select $select)
             use ($accountId) {
                $select
                    ->where(['account_id' => $accountId])
                    ->order(['date_posted ASC']);
            });
    }

    public function saveComment(Comment $comment)
    {
        $data = [
            'news_id'     => $comment->getNewsId(),
            'account_id'  => $comment->getAccountId(),
            'content'     => $comment->getContent(),
            'date_posted' => $comment->getDatePosted(),
        ];

        $id = $comment->getid();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getComment($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Comment id does not exist');
            }
        }
    }

    public function deleteComment(int $id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }

}
