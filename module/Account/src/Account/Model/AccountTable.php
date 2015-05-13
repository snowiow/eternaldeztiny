<?php
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class AccountTable
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
        $resultSet = $this->tableGateway->select();

        return $resultSet;
    }

    /**
     *
     * @param string|int $id
     *
     * @return array|\ArrayObject|null the account with the given id
     */
    public function getAccount($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Returns the data requested by the array. Key of array must be column name of Account table and $value the actual value
     * @param array $array with one [key => value] pair
     *
     * @return array|\ArrayObject|null the account with the given email
     */
    public function getAccountBy($array) {
        $rowset = $this->tableGateway->select($array);
        $row = $rowset->current();
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
    public function saveAccount(Account $account)
    {
        $data = [
            'name'            => $account->getName(),
            'password'        => $account->getPassword(),
            'userhash'        => $account->getUserHash(),
            'email'           => $account->getEmail(),
            'role'            => $account->getRole(),
            'avatar'          => $account->getAvatar(),
            'date_registered' => $account->getDateRegistered(),
        ];

        if (!$account->getId()) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAccount($account->getId())) {
                $this->tableGateway->update($data, ['id' => $account->getId()]);
            } else {
                throw new \Exception('Account id does not exist');
            }
        }
    }

    /**
     * Deletes the account with the given id
     * @param int|string $id
     */
    public function deleteAccount($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
