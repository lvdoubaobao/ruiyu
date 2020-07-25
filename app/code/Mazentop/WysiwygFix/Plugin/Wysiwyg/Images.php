<?php
namespace Mazentop\WysiwygFix\Plugin\Wysiwyg;
class Images
{
    public function afterGetImageHtmlDeclaration($subject, $html)
    {
        return urldecode($html);
    }
}
