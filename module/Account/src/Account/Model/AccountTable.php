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

    public function getAccountByName($name) {
        $rowset = $this->tableGateway->select(['name' => $name]);
        $row = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    public function getAccountByMail($email) {
        $rowset = $this->tableGateway->select(['email' => $email]);
        $row = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    public function saveAccount(Account $account)
    {
        $data = [
            'name'            => $account->name,
            'password'        => $account->password,
            'userhash'        => $account->userhash,
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
