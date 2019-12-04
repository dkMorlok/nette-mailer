<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Nette\Mail\IMailer;
use Nette\Mail\SendException;

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


	public function __construct(
		ITemplateMessageFactory $messageFactory,
		IMailer $mailer
	)
	{
		$this->messageFactory = $messageFactory;
		$this->mailer = $mailer;
	}


	/**
	 * @param string[] $emails
	 */
	public function setEmails(array $emails): void
	{
		$this->emails = $emails;
	}


	/**
	 * Generate and send message.
	 * @param array $params
	 *    Special parameters:
	 *    - filters (array of callback)
	 *    - attachments (array name => content)
	 * @param string|array|null $to
	 */
	public function send(
		string $template,
		string $lang,
		array $params,
		array $to,
		?string $from = null,
		bool $single = true,
		array $headers = []
	): ?TemplateMessage
	{
		$message = $this->messageFactory->create($template, $lang);

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

		$emails = \is_array($to) ? $to : ($to ? [$to] : []);
		if ($this->enabled && \count($emails) > 0) {
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


	protected function applyParams(TemplateMessage $message, array $params): void
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


	protected function applyHeaders(TemplateMessage $message, array $headers): void
	{
		foreach ($headers as $name => $value) {
			$message->getMail()->setHeader($name, $value);
		}
	}


	protected function applyFilters(TemplateMessage $message, array $filters): void
	{
		foreach ($filters as $name => $callback) {
			$message->getTemplate()->addFilter($name, $callback);
		}
	}


	protected function applyAttachments(TemplateMessage $message, array $attachments): void
	{
		foreach ($attachments as $name => $content) {
			$message->getMail()->addAttachment($name, $content);
		}
	}


	/**
	 * Method to send email via mailer
	 */
	private function sendMessage(TemplateMessage $templateMessage): void
	{
		try {
			$this->mailer->send($templateMessage->getMail());
		} catch (SendException $e) {
			throw new MailerException($e->getMessage(), null, $e);
		}
	}

}
