# 2.0.* -> 2.1.*

## Deprecated classes and interfaces:

* `TemplateMessage`, `ITemplateMessageFactory`, `TemplateMessageFactory`
  * there won't be any replacement for these
* `Mailer`
  * use `TemplateMailer` class and `ITemplateMailer` interface instead  
* `MailerExtension`
  * use `DI\MailerExtension` instead
     
## New Classes and Interfaces

* `ITemplateMailer`
  * similar to `Mailer` but does not return anything, just throws `MailerException` on error
  * passing filters to template as `$params['filters']` is not supported
    * provide all filters to your template factory in advance
  * passing attachments through `$params['attachments']` is not supported
    * an optional argument `$attachments` is added to the method instead

* `TemplateMailer`
  * default implementation of `ITemplateMailer`

* `DisabledMailer`
  * implementation if `ITemplateMailer` that does nothing and never throws

* `ITemplateRenderer`
  * abstracts the way of rendering email body given a template name and data

* `TemplateRendererException`
  * to be thrown by `ITemplateRenderer` implementations

* `LegacyTemplateRenderer`
  * renderer that uses the old `ITemplateFactory` to render latte template
  * will be deprecated in next major version along with associated symbols

* `TemplateRendererSelector`
  * renderer that allows switching between various renderers based on template name.

* `DI\MailerExtension`
  * replaces `MailerExtension`
  * none of the deprecated classes is added to the container
  * the new and non-deprecated classes are added to the container
  * all previous config options still work
  * `defaultRenderer` option can be set to an `ITemplateRenderer` service
    * it is set to the `LegacyTemplateRenderer` by default
  * `renderers` option can be set to map of string to `ITemplateRenderer` instances
  * `templateRenderers` can be set to map of renderer names to array of template names
    * the keys will be used to select a renderer from the `renderers` array that will render a given template
    * templates not provided will be rendered by the renderer set as `defaultRenderer`

