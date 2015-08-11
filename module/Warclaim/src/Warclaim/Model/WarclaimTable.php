<?php

namespace Warclaim\Model;

use Zend\Db\TableGateway\TableGateway;

use Warclaim\Model\Warclaim;

class WarclaimTable
{
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
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
