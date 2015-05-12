<?php

namespace AppMail\Service;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use AppMail\Model\AppMail;

class AppMailService implements AppMailServiceInterface
{
    /**
     * @var AdapterInterface
     */
    protected $dbAdapter;

    /**
     * @var HydratorInterface;
     */
    protected $hydrator;

    /**
     * @var AppMail
     */
    protected $appMailPrototype;

    public function __construct(AdapterInterface $dbAdapter, HydratorInterface $hydrator, AppMail $appMailPrototype)
    {
        $this->dbAdapter = $dbAdapter;
        $this->hydrator = $hydrator;
        $this->appMailPrototype = $appMailPrototype;
    }

    public function sendMail($to, $subject, $content)
    {
        $appMailData = $this->getAppMailData();
        $message = new Message();
        $message->addTo($to)
            ->addFrom($appMailData->getLogin())
            ->setSubject($subject)
            ->setBody($content);

        $transport = new SmtpTransport();
        $options = new SmtpOptions([
            'name'              => $appMailData->getHost(),
            'host'              => $appMailData->getHost(),
            'connection_class'  => 'login',
            'connection_config' => [
                'username' => $appMailData->getLogin(),
                'password' => $appMailData->getPassword(),
                'ssl' => 'tls'
            ],
        ]);
        $transport->setOptions($options);
        $transport->send($message);
    }

    /**
     * @return AppMail
     */
    private function getAppMailData()
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('app_mail');
        $select->where(['name = ?' => 'app']);

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), $this->appMailPrototype);
        }
        throw new \InvalidArgumentException("Mail-Account with given name: app not found.");
    }
}