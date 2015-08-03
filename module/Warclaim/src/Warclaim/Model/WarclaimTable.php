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
            'name'     => $warclaim->getId(),
            'password' => $warclaim->getAssignments(),
        ];
        if (!$account->getId()) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAccount($warclaim->getId())) {
                $this->tableGateway->update($data, ['id' => $warclaim->getId()]);
            } else {
                throw new \Exception('Warclaim id does not exist');
            }
        }
    }

}
