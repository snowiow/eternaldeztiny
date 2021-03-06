<?php
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Application\Constants;

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
        return $this->tableGateway->select();
    }

    public function getUsersAndAbove(bool $paginated, $name = '', $roles = [])
    {
        $select = new Select('account');
        $where  = new Where();

        if ($roles) {
            $sub = $where->nest();
            for ($i = 0; $i < count($roles); $i++) {
                $sub->equalTo('role', $roles[$i]);
                if ($i < count($roles) - 1) {
                    $sub->or;
                }
            }
            $sub->unnest();
        } else {
            $where->greaterThan('role', '0');
            $where->lessThan('role', '32');
        }
        if ($name) {
            $where->like('name', '%' . $name . '%');
        }

        $select->where($where)
            ->order('name ASC');

        if ($paginated) {
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Account());
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            return new Paginator($paginatorAdapter);
        }
        return $this->tableGateway->select($select);
    }
    /**
     *
     * @param string|int $id
     *
     * @return array|\ArrayObject|null the account with the given id
     */
    public function getAccount($id)
    {
        $id     = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Returns the data requested by the array. Key of array must be column name of Account table and $value the actual value
     * @param array $array with one [key => value] pair
     *
     * @return array|\ArrayObject|null|Account the account with the given email
     */
    public function getAccountBy($array)
    {
        $rowset = $this->tableGateway->select($array);
        $row    = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    public function getMembers()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->where('role > 1')
                ->order('name ASC');
        });
    }

    public function getMembersWithWarstatus()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select
                ->join(
                    ['w' => 'warstatus'],
                    'w.id = account.id',
                    [
                        'opted_in_date',
                        'opted_out_date',
                        'reason',
                        'gemable',
                        'crusade',
                        'opted_in_date_mini',
                        'opted_out_date_mini',
                        'reason_mini',
                        'gemable_mini',
                        'crusade_mini',
                    ]
                )
                ->where('role > 1');
        });
    }

    public function getLeadershipMails()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->where('role > 3')->columns(['email']);
        });
    }

    /**
     * Returns accounts which names or minis fit to the given array
     * @param array $names the names or minis of which the accounts are needed
     * @return null|Account the found accounts or null
     */
    public function getAccountsFromNames(array $names)
    {
        if (!$names) {
            return null;
        }
        $where = 'name = "' . $names[0] . '" OR mini = "' . $names[0] . '"';
        for ($i = 1; $i < count($names); $i++) {
            $where .= ' OR name = "' . $names[$i] . '"';
            $where .= ' OR mini = "' . $names[$i] . '"';
        }
        return $this->tableGateway->select(function (Select $select) use ($where) {
            $select->where($where);
        });
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
            'mini'            => $account->getMini(),
        ];
        if (!$account->getId()) {
            $data['password'] = hash('sha256', $account->getPassword()) . Constants::SALT;
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
        $account = $this->getAccount($id);
        $dir     = getcwd() . '/public/users/' . $account->getName();
        if (file_exists($dir)) {
            $this->rrmdir($dir);
        }
        $this->tableGateway->delete(['id' => $id]);
    }

    private function rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }

        }
        rmdir($dir);
    }
}
