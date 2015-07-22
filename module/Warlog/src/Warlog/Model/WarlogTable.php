<?php
namespace Warlog\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class WarlogTable
{
    /**
     * @var \Zend\Db\TableGateway\TableGateway
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
     * Returns all Accounts in db
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     *
     * @param string|int $id
     *
     * @return array|\ArrayObject|null the account with the given id
     */
    public function getWarlog()
    {
        $rowset = $this->tableGateway->select([1]);
        $row    = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Saves an account to the db. If the id exists the given dataset will be updated
     * @param \Account\Model\Account $account
     *
     * @throws \Exception
     */
    public function saveWarlog(Warlog $warlog)
    {
        $data = [
            'wins'   => $warlog->getWins(),
            'losses' => $warlog->getLosses(),
            'draws'  => $warlog->getDraws(),
        ];
        if (!$warlog->getId()) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getWarlog()) {
                $this->tableGateway->update($data, ['id' => $warlog->getId()]);
            } else {
                throw new \Exception('Account id does not exist');
            }
        }
    }
}
