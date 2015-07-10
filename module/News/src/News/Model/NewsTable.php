<?php

namespace News\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class NewsTable {
	/**
	 * @var TableGateway
	 */
	protected $tableGateway;

	/**
	 * @param TableGateway $tableGateway
	 */
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Returns all News in db
	 *
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll($paginated = false) {
		if ($paginated) {
			$select = new Select('news');
			$select->join(['a' => 'account'], 'news.account_id = a.id', ['name'])->order('date_posted DESC');
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new News());
			$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
			return new Paginator($paginatorAdapter);
		}
		return $this->tableGateway->select(function (Select $select) {
			$select
			->join(['a' => 'account'], 'news.account_id = a.id', ['name'])
			->order('date_posted DESC');
		});
	}

	/**
	 * Get the news with the corresponding id
	 *
	 * @param int|string $id
	 *
	 * @return array|\ArrayObject|null
	 * @throws \Exception
	 */
	public function getNews(int $id) {
		$rowset = $this->tableGateway->select(function (Select $select) use ($id) {
			$select
			->join(['a' => 'account'], 'news.account_id = a.id', ['name'])
			->where(['news.id' => $id]);
		});

		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}

		return $row;
	}

	/**
	 * Saves the given news object to the db
	 *
	 * @param \News\Model\News $news
	 *
	 * @throws \Exception
	 */
	public function saveNews(News $news) {
		$data = [
			'account_id' => $news->getAccountId(),
			'title' => $news->getTitle(),
			'content' => $news->getContent(),
			'date_posted' => $news->getDatePosted(),
		];

		$id = (int) $news->getId();
		if ($id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if ($this->getNews($id)) {
				$this->tableGateway->update($data, ['id' => $id]);
			} else {
				throw new \Exception('News id does not exist');
			}
		}
	}

	/**
	 * Deletes the news with the corresponding id.
	 *
	 * @param int|string $id
	 */
	public function deleteNews($id) {
		$this->tableGateway->delete(['id' => (int) $id]);
	}
}
