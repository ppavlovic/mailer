<?php

namespace G4\Mailer\Message;


class ZendMessageFacade
{
    const ENCODING  = 'utf-8';
    const TYPE_HTML = 'text/html';
    const TYPE_TEXT = 'text/plain';

    /**
     * @param \G4\Mailer\Message $message
     * @return \Zend\Mail\Message
     */
    public static function convert(\G4\Mailer\Message $message)
    {
        $htmlPart = new \Zend\Mime\Part($message->getHtmlBody());
        $htmlPart->charset = self::ENCODING;
        $htmlPart->type = self::TYPE_HTML;

        $textPart = new \Zend\Mime\Part($message->getTextBody());
        $textPart->charset = self::ENCODING;
        $textPart->type = self::TYPE_TEXT;

        $body = new \Zend\Mime\Message();
        $body->setParts([$textPart, $htmlPart]);

        $zendMessage = new \Zend\Mail\Message();
        $zendMessage
            ->addTo($message->getTo())
            ->addFrom(
                self::getFromEmail($message->getFrom()),
                self::getFromName($message->getFrom())
            )
            ->setSubject($message->getSubject())
            ->setBody($body)
            ->setEncoding(self::ENCODING)
            ->getHeaders()->get('content-type')->setType('multipart/alternative');

        if (count($message->getCc())) {
            $zendMessage->addCc($message->getCc());
        }
        if (count($message->getBcc())) {
            $zendMessage->addBcc($message->getBcc());
        }

        return $zendMessage;
    }

    private static function getFromEmail($from)
    {
        if (preg_match('/(.*) <(.*)>/', $from, $regs)) {
            // match Sender <email@example.com>
            return $regs[2];
        }
        return $from;
    }

    private static function getFromName($from)
    {
        if (preg_match('/(.*) <(.*)>/', $from, $regs)) {
            // match Sender <email@example.com>
            return $regs[1];
        }
        return null;
    }
}