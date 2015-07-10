<?php

namespace ApplyNow\Model;

use Zend\Db\TableGateway\TableGateway;

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
    public function getApplication($id)
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
     * @param Application $application to be saved
     *
     * @throws \Exception
     */
    public function saveApplication(Application $application)
    {
        $data = [
            'name' => $application->getName(),
            'tag' => $application->getTag(),
            'email' => $application->getEmail(),
            'strategies' => $application->getStrategies(),
            'th' => $application->getTh(),
            'warStars' => $application->getWarStars(),
            'age' => $application->getAge(),
            'infos' => $application->getInfos(),
            'why' => $application->getWhy(),
            'basePic' => $application->getBasePic(),
            'profilePic' => $application->getProfilePic(),
            'processed' => $application->isProcessed(),
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
