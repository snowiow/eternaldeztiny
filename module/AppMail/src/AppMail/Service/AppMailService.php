<?php

namespace AppMail\Service;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mime;
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

    /**
     * @param \Zend\Db\Adapter\AdapterInterface       $dbAdapter
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \AppMail\Model\AppMail                  $appMailPrototype
     */
    public function __construct(AdapterInterface $dbAdapter, HydratorInterface $hydrator, AppMail $appMailPrototype)
    {
        $this->dbAdapter        = $dbAdapter;
        $this->hydrator         = $hydrator;
        $this->appMailPrototype = $appMailPrototype;
    }

    /**
     * Sends a mail with the given data from the AppMailAccount
     * @param string $to
     * @param string $subject
     * @param string $content
     */
    public function sendMail(string $to, string $subject, string $content, array $files = [])
    {
        $content .= "\n\nThis is an automated mail. Please don't respond.";
        $text              = new Mime\Part($content);
        $text->type        = 'text/plain';
        $text->charset     = 'utf-8';
        $text->disposition = Mime\Mime::DISPOSITION_INLINE;

        $parts[] = $text;
        foreach ($files as $filePath) {
            $fileContent             = file_get_contents($filePath);
            $attachment              = new Mime\Part($fileContent);
            $attachment->type        = 'image/' . pathinfo($filePath, PATHINFO_EXTENSION);
            $attachment->filename    = basename($filePath);
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding    = Mime\Mime::ENCODING_BASE64;
            $parts[]                 = $attachment;
        }
        $mime = new Mime\Message();
        $mime->setParts($parts);

        $appMailData = $this->getAppMailData();
        $message     = new Message();
        $message->addTo($to)
            ->addFrom($appMailData->getAdress())
            ->setSubject($subject)
            ->setBody($mime)
            ->setEncoding('utf-8');

        $transport = new SmtpTransport();
        $options   = new SmtpOptions([
            'name'              => $appMailData->getHost(),
            'host'              => $appMailData->getHost(),
            'connection_class'  => 'login',
            'connection_config' => [
                'username' => $appMailData->getLogin(),
                'password' => $appMailData->getPassword(),
            ],
        ]);
        $transport->setOptions($options);
        $transport->send($message);
    }

    /**
     * Retrieves the login data for the mailbox which is used by the app
     * @return AppMail
     * @throws InvalidArgumentException if the mailbox doesn't exist
     */
    private function getAppMailData()
    {
        $sql    = new Sql($this->dbAdapter);
        $select = $sql->select('app_mail');
        $select->where(['name = ?' => 'app']);

        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), $this->appMailPrototype);
        }
        throw new \InvalidArgumentException("Mail-Account with given name: app not found.");
    }
}
