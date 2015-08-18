<?php

namespace Warclaim\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

use Warclaim\Model\Warclaim;

class WarclaimTable
{
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Get the warclaim with the corresponding id
     *
     * @param int|string $id
     *
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getWarclaim(int $id)
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     *
     * @param string|int $id
     *
     * @return array|\ArrayObject|null the warclaim with the given id
     */
    public function getCurrentWar()
    {
        $rowset = $this->tableGateway->select(function (Select $select) {
            $select->where('open = 1');
        });
        $row = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;

    }

    /**
     * Saves a Warclaim to the db. If the id exists the given dataset will be updated
     * @param Warclaim\Model\Warclaim $warclaim
     *
     * @throws \Exception
     */
    public function saveWarclaim(Warclaim $warclaim)
    {
        $data = [
            'id'          => $warclaim->getId(),
            'size'        => $warclaim->getSize(),
            'opponent'    => $warclaim->getOpponent(),
            'strategy'    => $warclaim->getStrategy(),
            'assignments' => serialize($warclaim->getAssignments()),
            'cleanup'     => serialize($warclaim->getCleanup()),
            'info'        => serialize($warclaim->getInfo()),
            'open'        => $warclaim->isOpen(),
        ];
        if (!$warclaim->getId()) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getWarclaim((int) $warclaim->getId())) {
                $this->tableGateway->update($data, ['id' => $warclaim->getId()]);
            } else {
                throw new \Exception('Warclaim id does not exist');
            }
        }
    }
}
