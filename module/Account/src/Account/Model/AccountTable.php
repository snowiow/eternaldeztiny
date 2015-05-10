<?php
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;

class AccountTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();

        return $resultSet;
    }

    public function getAccount($name)
    {
        $rowset = $this->tableGateway->select(['name' => $name]);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $name");
        }

        return $row;
    }

    public function saveAccount(Account $account)
    {
        $data = [
            'name'            => $account->name,
            'password'        => $account->password,
            'email'           => $account->email,
            'role'            => $account->role,
            'avatar'          => $account->avatar,
            'date_registered' => $account->date_registered,
        ];

        if ($account->name) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAccount($account->name)) {
                $this->tableGateway->update($data, ['name' => $account->name]);
            } else {
                throw new \Exception('Account name does not exist');
            }
        }
    }

    public function deleteAccount($name)
    {
        $this->tableGateway->delete(['name' => $name]);
    }
}
