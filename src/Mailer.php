<?php

namespace Smartsupp\Mailer;

class Mailer
{

	/** @var boolean */
	public $enabled = true;

	/** @var ITemplateMessageFactory */
	private $factory;

	/** @var Sender */
	private $sender;


	/**
	 * @param ITemplateMessageFactory $factory
	 * @param Sender $sender
	 */
	public function __construct(ITemplateMessageFactory $factory, Sender $sender)
	{
		$this->factory = $factory;
		$this->sender = $sender;
	}


	/**
	 * @return Sender
	 */
	public function getSender()
	{
		return $this->sender;
	}


	/**
	 * Check if mail is sendable.
	 * @param $name
	 * @return bool
	 */
	public function isSendable($name)
	{
		return $this->factory->isCreateable($name);
	}


	/**
	 * Send predefined email to user
	 *
	 * @param string $name
	 * @param array $params
	 *        Special parameters:
	 *            - helperLoaders (array of callback)
	 *            - helpers (array of callback)
	 *            - attachments (array name=>content)
	 * @param string|array $to
	 * @param string $from
	 * @param boolean $single
	 * @param array $headers
	 * @return TemplateMessage
	 */
	public function send($name, array $params, $to = null, $from = null, $single = true, array $headers = null)
	{
		$message = $this->factory->create($name);
		if (!$message) {
			return null;
		}

		$template = $message->getTemplate();

		if (isset($params['filters'])) {
			foreach ($params['filters'] as $name => $callback) {
				$template->addFilter($name, $callback);
			}
			unset($params['filters']);
		}

		if (isset($params['attachments'])) {
			foreach ($params['attachments'] as $name => $content) {
				$message->addAttachment($name, $content);
			}
			unset($params['attachments']);
		}

		$template->setParameters($params);
		$message->applyTemplate();

		if ($headers) {
			foreach ($headers as $name => $value) {
				$message->setHeader($name, $value);
			}
		}

		if ($this->enabled && $to) {
			$this->sender->send($message, $to, $from, $single);
		}

		return $message;
	}

}