<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

use Nette\Mail\Mailer as NetteMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;

class TemplateMailer implements ITemplateMailer
{

    private NetteMailer $mailer;

    private IMessageFactory $messageFactory;


    /**
     * @param NetteMailer $mailer
     * @param IMessageFactory $messageFactory
     */
    public function __construct(
        NetteMailer $mailer,
        IMessageFactory $messageFactory
    ) {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
    }


    public function send(
        string $template,
        string $lang,
        array $params,
        array $to,
        ?string $from = null,
        bool $single = true,
        array $headers = [],
        array $attachments = []
    ): void {
        if (!$to) {
            return;
        }

        $headers = $this->removeDuplicateEmails($to, $headers);

        $message = $this->messageFactory->createMessage(
            $template,
            $lang,
            $params,
            $from,
            $headers,
            $attachments
        );

        if ($single) {
            foreach ($to as $email) {
                $message->clearHeader('To');
                $message->addTo($email);
                $this->sendMessage($message);
            }
        } else {
            $message->clearHeader('To');
            foreach ($to as $email) {
                $message->addTo($email);
            }
            $this->sendMessage($message);
        }
    }

    /**
     * Removes any duplicate emails from Cc and Bcc headers if also in To
     *
     * First from Cc is removed what is in To
     * Then from Bcc is removed what is in To or Cc
     *
     * Effectively, if same email is in:
     * - To and Cc => remove from Cc
     * - To and Bcc => remove from Bcc
     * - Cc and Bcc => remove from Bcc
     * - To, Cc and Bcc => remove from Cc and Bcc
     *
     * @param string[] $to
     * @param array $headers
     * @return array Returns the modified headers
     */
    private function removeDuplicateEmails(array $to, array $headers): array
    {
        $ccs = isset($headers['Cc']) && is_array($headers['Cc']) ? $headers['Cc'] : [];
        $bccs = isset($headers['Bcc']) && is_array($headers['Bcc']) ? $headers['Bcc'] : [];

        $ccs = \array_diff_key($ccs, $to);
        $bccs = \array_diff_key($bccs, $ccs, $to);

        if (empty($ccs)) {
            unset($headers['Cc']);
        } else {
            $headers['Cc'] = $ccs;
        }
        if (empty($bccs)) {
            unset($headers['Bcc']);
        } else {
            $headers['Bcc'] = $bccs;
        }
        return $headers;
    }


    private function sendMessage(Message $message): void
    {
        try {
            $this->mailer->send($message);
        } catch (SendException $e) {
            throw new MailerException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
