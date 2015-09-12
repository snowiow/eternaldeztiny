<?php

namespace News\Model;

use Zend\Db\TableGateway\TableGateway;

use News\Model\NewsCategory;

class NewsCategoryTable
{

    /**
     * @var TableGateway
     */
    private $tableGateway;

    /**
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Returns the data requested by the array. Key of array must be column name of Account table and $value the actual value
     * @param array $array with one [key => value] pair
     *
     * @return array|\ArrayObject|null|Account the account with the given email
     */
    public function getNewsCategoryBy($array)
    {
        $rowset = $this->tableGateway->select($array);
        $row    = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    /**
     * Saves the given NewsCategory object to the db
     *
     *  @param NewsCategory
     *  @throws \Exception
     */
    public function saveNewsCategory(NewsCategory $nc)
    {
        $data = [
            'name' => $nc->getName(),
        ];

        $id = (int) $nc->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getNews($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Ȩxception('NewsCategory id does not exist');
            }
        }
    }
}
