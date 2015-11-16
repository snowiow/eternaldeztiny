<?php

namespace ApplyNow\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ApplicationTable
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Returns the Application by the given id
     * @param int $id
     */
    public function getApplication(\int $id)
    {
        $id     = $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    public function getLastInsertedId()
    {
        return $this->tableGateway->lastInsertValue;
    }

    /**
     * Returns all open applications
     */
    public function getOpenApplications()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select
                ->where('processed = 0')
                ->order('date_applied ASC');
        });
    }

    /**
     * Returns all accepted and declined applications
     */
    public function getProcessedApplications($paginated = false)
    {
        if ($paginated) {
            $select = new Select('application');
            $select
                ->join(['a' => 'account'],
                    'application.processed_by = a.id',
                    ['account_name' => 'name'])
                ->where('processed > 0')
                ->order('date_applied DESC');

            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Application());
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            return new Paginator($paginatorAdapter);
        }
        return $this->tableGateway->select(function (Select $select) {
            $select
                ->join(['a' => 'account'], 'application.processed_by = a.id', ['account_name' => 'name'])
                ->where('processed > 0')
                ->order('date_applied DESC');
        });
    }

    public function getApplicationsByAccountId(int $id)
    {
        return $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where('application.processed_by = ' . $id);
        });
    }

    /**
     * @param Application $application to be saved
     *
     * @throws \Exception
     */
    public function saveApplication(Application $application)
    {
        $data = [
            'name'         => $application->getName(),
            'tag'          => $application->getTag(),
            'email'        => $application->getEmail(),
            'strategies'   => $application->getStrategies(),
            'th'           => $application->getTh(),
            'warStars'     => $application->getWarStars(),
            'age'          => $application->getAge(),
            'infos'        => $application->getInfos(),
            'why'          => $application->getWhy(),
            'niceAndTidy'  => $application->getNiceAndTidy(),
            'spoilsOfWar'  => $application->getSpoilsOfWar(),
            'goldGrab'     => $application->getGoldGrab(),
            'basepic'      => $application->getBasepic(),
            'profilepic'   => $application->getProfilepic(),
            'processed'    => $application->getProcessed(),
            'processed_by' => $application->getProcessedBy(),
            'date_applied' => $application->getDateApplied(),
        ];

        $id = (int) $application->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getApplication($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Application id does not exist');
            }
        }
    }
}
