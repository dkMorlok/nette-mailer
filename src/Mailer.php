<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Nette\Mail\IMailer;

class Mailer
{

	/** @var boolean */
	public $enabled = true;

	/** @var ITemplateMessageFactory */
	private $messageFactory;

	/** @var IMailer */
	private $mailer;

	/** @var string[] */
	private $emails;


	/**
	 * @param ITemplateMessageFactory $messageFactory
	 * @param IMailer $mailer
	 */
	public function __construct(ITemplateMessageFactory $messageFactory, IMailer $mailer)
	{
		$this->messageFactory = $messageFactory;
		$this->mailer = $mailer;
	}


	/**
	 * @param string[] $emails
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
	 * @return ITemplateMessageFactory
	 */
	public function getMessageFactory()
	{
		return $this->messageFactory;
	}


	/**
	 * Generate and send message.
	 * @param string $name
	 * @param array $params
	 * 	Special parameters:
	 *  	- filters (array of callback)
	 *		- attachments (array name=>content)
	 * @param string|array $to
	 * @param string $from
	 * @param boolean $single
	 * @param array $headers
	 * @return TemplateMessage
	 * @throws MailerException
	 */
	public function send($name, array $params, $to = null, $from = null, $single = true, array $headers = [])
	{
		$message = $this->createMessage($name);

		$this->applyParams($message, $params);
		$this->applyHeaders($message, $headers);

		$message->render();

		if ($from === null) {
			$message->getMail()->setFrom($this->emails['default']);
		} elseif (isset($this->emails[$from])) {
			$message->getMail()->setFrom($this->emails[$from]);
		} else {
			$message->getMail()->setFrom($from);
		}

		$emails = is_array($to) ? $to : ($to ? [$to] : []);
		if ($this->enabled && count($emails) > 0) {
			if ($single) {
				foreach ($emails as $email) {
					$message->getMail()->clearHeader('To');
					$message->getMail()->addTo($email);
					$this->sendMessage($message);
				}
			} else {
				$message->getMail()->clearHeader('To');
				foreach ($emails as $email) {
					$message->getMail()->addTo($email);
				}
				$this->sendMessage($message);
			}
		}

		return $message;
	}


	/**
	 * @param string $name
	 * @return TemplateMessage
	 */
	protected function createMessage($name)
	{
		return $this->messageFactory->create($name);
	}


	/**
	 * @param TemplateMessage $message
	 * @param array $params
	 */
	protected function applyParams(TemplateMessage $message, array $params)
	{
		if (isset($params['filters'])) {
			$this->applyFilters($message, $params['filters']);
			unset($params['filters']);
		}
		if (isset($params['attachments'])) {
			$this->applyAttachments($message, $params['attachments']);
			unset($params['attachments']);
		}
		$message->setParameters($params);
	}


	/**
	 * @param TemplateMessage $message
	 * @param array $headers
	 */
	protected function applyHeaders($message, array $headers)
	{
		foreach ($headers as $name => $value) {
			$message->getMail()->setHeader($name, $value);
		}
	}


	/**
	 * @param TemplateMessage $message
	 * @param array $filters
	 */
	protected function applyFilters($message, array $filters)
	{
		foreach ($filters as $name => $callback) {
			$message->getTemplate()->addFilter($name, $callback);
		}
	}


	/**
	 * @param TemplateMessage $message
	 * @param array $attachments
	 */
	protected function applyAttachments($message, array $attachments)
	{
		foreach ($attachments as $name => $content) {
			$message->getMail()->addAttachment($name, $content);
		}
	}


	/**
	 * Method to send email via mailer. Can be used to handle exceptions.
	 * @param TemplateMessage $templateMessage
	 * @throws MailerException
	 */
	private function sendMessage(TemplateMessage $templateMessage)
	{
		try {
			$this->mailer->send($templateMessage->getMail());
		} catch (\Exception $e) {
			throw new MailerException($e->getMessage(), null, $e);
		}
	}

}
