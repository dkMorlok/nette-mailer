# 2.0 -> 2.1

## New Classes and Interfaces

* `ITemplateMailer`
  * similar to `Mailer` but does not return anything, just throws `MailerException` on error
  * passing filters to template as `$params['filters']` is not supported
    * provide all filters to your template factory in advance
    * `LegacyTemplateRenderer` still supports this feature but it may be deprecated in next major version (3.0)
  * passing attachments through `$params['attachments']` is not supported
    * an optional argument `$attachments` is added to the method instead

* `IMessageFactory`
  * creates `Nette\Mail\Message` instances using most of the `ITemplateMailer::send` arguments.

* `MessageFactory`
  * default implementation of `IMessageFactory`
  * contains all current behaviour of `Mailer` related to creating the message instance

* `TemplateMailer`
  * default implementation of `ITemplateMailer` using `IMessageFactory` and `Nette\Mail\Mailer`

* `DisabledMailer`
  * implementation if `ITemplateMailer` that does nothing and never throws

* `ITemplateRenderer`
  * abstracts the way of rendering email body given a template name and data

* `TemplateRendererException`
  * to be thrown by `ITemplateRenderer` implementations

* `LegacyTemplateRenderer`
  * renderer that uses the old `ITemplateFactory` to render latte template
  * may be deprecated in future major version and replaced with a proper latte renderer

* `TemplateRendererSelector`
  * renderer that allows switching between various renderers based on template name.

* `DI\MailerExtension`
  * replaces `MailerExtension`
  * none of the deprecated classes is added to the container
  * the new and non-deprecated classes are added to the container
  * all previous config options still work but most are distributed among different services
  * `defaultRenderer` option can be set to an `ITemplateRenderer` service
    * it is set to the `LegacyTemplateRenderer` by default
  * `renderers` option can be set to map of string to `ITemplateRenderer` instances
  * `rendererTemplates` can be set to map of renderer names to array of template names
    * the keys will be used to select a renderer from the `renderers` array that will render a given template
    * templates not provided will be rendered by the renderer set as `defaultRenderer`

## Deprecated classes and interfaces will be removed in 3.0

* `TemplateMessage`, `ITemplateMessageFactory`, `TemplateMessageFactory`
  * use `IMessageFactory` and `MessageFactory`
* `Mailer`
  * use `TemplateMailer` class and `ITemplateMailer` interface instead  
* `MailerExtension`
  * use `DI\MailerExtension` instead
     
