<?php

namespace Clan\Model;

use Zend\Db\TableGateway\TableGateway;

class ClanTable
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * Constructs a Tablegateway
     * @param TableGateway $tableGateway the gateway to the clan table
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Get the clan with the corresponding id
     * @param int $id the id of the clan which should be retrieved from the db
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getClan(int $id)
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * Saves the given clan object to the db
     * @param Clan $clan the clan to be saved
     * @throws \Exception
     * @return void
     */
    public function saveNews(Clan $clan)
    {
        $data = $clan->getArrayCopy();
        $id   = (int) $clan->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getClan($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Clan id does not exist');
            }
        }
    }

    /**
     * Deletes the news with the corresponding id.
     * @param  int $id the id of the clan which should be deleted
     * @return void
     */
    public function deleteClan(int $id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
