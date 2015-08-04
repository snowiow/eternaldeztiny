<?php

namespace Media\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class MediaTable
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Returns all Media in db
     *
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll($paginated = false)
    {
        if ($paginated) {
            $select = new Select('media');
            $select->join(['a' => 'account'], 'media.account_id = a.id', ['name'])->order('date_posted DESC');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Media());
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            return new Paginator($paginatorAdapter);
        }
        return $this->tableGateway->select(function (Select $select) {
            $select
            ->join(['a' => 'account'], 'media.account_id = a.id', ['name'])
            ->order('date_posted DESC');
        });
    }

    /**
     * Get the media with the corresponding id
     *
     * @param int|string $id
     *
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getMedia(int $id)
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
            $select
            ->join(['a' => 'account'], 'media.account_id = a.id', ['name'])
            ->where(['media.id' => $id]);
        });

        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * Saves the given media object to the db
     *
     * @param \Media\Model\Media $media
     *
     * @throws \Exception
     */
    public function saveMedia(Media $media)
    {
        $data = [
            'account_id'  => $media->getAccountId(),
            'title'       => $media->getTitle(),
            'url'         => $media->getUrl(),
            'date_posted' => $media->getDatePosted(),
        ];

        $id = (int) $media->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getMedia($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Media id does not exist');
            }
        }
    }

    /**
     * Deletes the media with the corresponding id.
     *
     * @param int|string $id
     */
    public function deleteMedia($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
