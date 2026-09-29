<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Html_sanitizer
{
	private $allowedTags = array('p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'a', 'img', 'figure', 'figcaption', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'hr', 'span', 'div', 'pre', 'code');
	private $allowedAttributes = array(
		'*' => array('class', 'style'),
		'a' => array('href', 'title', 'target', 'rel'),
		'img' => array('src', 'alt', 'title', 'width', 'height'),
		'td' => array('colspan', 'rowspan'),
		'th' => array('colspan', 'rowspan', 'scope')
	);
	private $droppedWithContent = array('script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'select', 'textarea', 'svg', 'math', 'noscript', 'template', 'link', 'meta', 'base');

	public function clean($html)
	{
		$html = trim((string) $html);
		if ($html === '')
		{
			return '';
		}

		$previous = libxml_use_internal_errors(TRUE);
		$document = new DOMDocument('1.0', 'UTF-8');
		$document->loadHTML('<?xml encoding="utf-8"?><div data-sanitizer-root="1">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);

		$xpath = new DOMXPath($document);
		$root = $xpath->query('//div[@data-sanitizer-root]')->item(0);
		if (! $root instanceof DOMElement)
		{
			return '';
		}

		$this->cleanChildren($root);
		$output = '';
		foreach ($root->childNodes as $child)
		{
			$output .= $document->saveHTML($child);
		}
		return trim($output);
	}

	private function cleanChildren(DOMNode $node)
	{
		for ($i = $node->childNodes->length - 1; $i >= 0; $i--)
		{
			$child = $node->childNodes->item($i);
			if ($child instanceof DOMElement)
			{
				$tag = strtolower($child->nodeName);
				if (in_array($tag, $this->droppedWithContent, TRUE))
				{
					$node->removeChild($child);
					continue;
				}
				$this->cleanChildren($child);
				if (! in_array($tag, $this->allowedTags, TRUE))
				{
					$this->unwrap($child);
					continue;
				}
				$this->cleanAttributes($child, $tag);
			}
			elseif (! $child instanceof DOMText)
			{
				$node->removeChild($child);
			}
		}
	}

	private function unwrap(DOMElement $element)
	{
		$parent = $element->parentNode;
		while ($element->firstChild)
		{
			$parent->insertBefore($element->firstChild, $element);
		}
		$parent->removeChild($element);
	}

	private function cleanAttributes(DOMElement $element, $tag)
	{
		$allowed = $this->allowedAttributes['*'];
		if (isset($this->allowedAttributes[$tag]))
		{
			$allowed = array_merge($allowed, $this->allowedAttributes[$tag]);
		}

		for ($i = $element->attributes->length - 1; $i >= 0; $i--)
		{
			$attribute = $element->attributes->item($i);
			$name = strtolower($attribute->nodeName);
			$value = trim((string) $attribute->nodeValue);
			$remove = ! in_array($name, $allowed, TRUE);
			if (! $remove && ($name === 'href' || $name === 'src'))
			{
				$remove = ! $this->isSafeUrl($value, $name === 'href');
			}
			if (! $remove && $name === 'style')
			{
				$remove = (bool) preg_match('/expression|javascript|vbscript|url\s*\(|@import|behavior/i', $value);
			}
			if ($remove)
			{
				$element->removeAttribute($attribute->nodeName);
			}
		}

		if ($tag === 'a' && $element->getAttribute('target') === '_blank')
		{
			$element->setAttribute('rel', 'noopener noreferrer');
		}
	}

	private function isSafeUrl($url, $isLink)
	{
		$normalized = strtolower(preg_replace('/[\x00-\x20]+/', '', $url));
		if ($normalized === '')
		{
			return FALSE;
		}
		if (! preg_match('/^([a-z][a-z0-9+.-]*):/', $normalized, $matches))
		{
			return TRUE;
		}
		$schemes = $isLink ? array('http', 'https', 'mailto', 'tel') : array('http', 'https');
		return in_array($matches[1], $schemes, TRUE);
	}
}
