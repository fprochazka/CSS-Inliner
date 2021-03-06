<?php
namespace Northys;

use Symfony\Component\CssSelector\CssSelector,
	Sabberworm\CSS;

/**
 * Description of Inliner
 *
 * @author Northys
 */
class CSSInliner {
	/** @var Sabberworm\CSS\Parser */
	private $css;
	
	public function addCSS ($filename) {
		$css = file_get_contents($filename);
		if (!$css) {
			throw new \Exception("Failed on loading CSS file. Check the file path you have provided!", 1);
		}
		$this->css = new CSS\Parser($css);
		$this->css = $this->css->parse();
	}
	
	public function render ($html, $return = FALSE) {
		$dom = new \DOMDocument;
		$dom->loadHTML($html);
		$finder = new \DOMXPath($dom);
		
		foreach ($this->css->getAllRuleSets() as $ruleSet) {
			$selector = $ruleSet->getSelector();
			foreach ($finder->evaluate(CssSelector::toXPath($selector[0])) as $node) {
				if ($node->getAttribute('style')) {
					$node->setAttribute('style', $node->getAttribute('style') . implode(' ', $ruleSet->getRules()));
				} else {
					$node->setAttribute('style', implode(' ', $ruleSet->getRules()));
				}
			}
		}
		if ($return == TRUE) {
			return $dom->saveHTML();
		} else {
			echo $dom->saveHTML();
		}
	}
}
