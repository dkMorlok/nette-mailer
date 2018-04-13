<?php

namespace Smartsupp\Mailer;

use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Tracy;

class Sender
{

	/** @var boolean */
	public $catchExceptions = false;

	/** @var string[] */
	private $emails;

	/** @var IMailer */
	private $mailer;


	public function __construct(IMailer $mailer)
	{
		$this->mailer = $mailer;
	}


	/**
	 * set from emails
	 * @param array $emails
	 */
	public function setEmails(array $emails)
	{
		$this->emails = $emails;
	}


	/**
	 * @return IMailer
	 */
	public function getMailer()
	{
		return $this->mailer;
	}


	/**
	 * Send message
	 * @param Message $message
	 * @param string|array $emails
	 * @param string $from
	 *        NULL - send from default email,
	 *        name - send from "name" email,
	 *        email - send from email
	 * @param boolean $single
	 * @return boolean
	 */
	public function send(Message $message, $emails, $from = null, $single = true)
	{
		$emails = is_array($emails) ? $emails : array($emails);

		if ($from === null) {
			$message->setFrom($this->emails["default"]);
		} elseif (isset($this->emails[$from])) {
			$message->setFrom($this->emails[$from]);
		} else {
			$message->setFrom($from);
		}

		if ($single) {
			foreach ($emails as $email) {
				$message->clearHeader('To');
				$message->addTo($email);
				$this->sendMessage($message);
			}
		} else {
			$message->clearHeader('To');
			foreach ($emails as $email) {
				$message->addTo($email);
			}
			$this->sendMessage($message);
		}
	}


	protected function sendMessage(Message $message)
	{
		try {
			$this->mailer->send($message);
		} catch (\Exception $e) {
			if (strpos($e->getMessage(), 'SMTP server did not accept RCPT TO') !== false) {
				// don't log exception, only store information
				Tracy\Debugger::log($e->getMessage(), 'mailer');
			} else {
				// all other errors log as exceptions
				Tracy\Debugger::log($e, 'mailer');
			}
			if (!$this->catchExceptions) {
				throw $e;
			}
		}
	}

}
