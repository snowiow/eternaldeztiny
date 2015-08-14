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
            'open'        => $warclaim->isOpen(),
        ];
        if (!$warclaim->getId()) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getWarclaim($warclaim->getId())) {
                $this->tableGateway->update($data, ['id' => $warclaim->getId()]);
            } else {
                throw new \Exception('Warclaim id does not exist');
            }
        }
    }
}
