<?php
namespace Foxway;
/**
 * outPrint class.
 *
 * @file outPrint.php
 * @ingroup Foxway
 * @author Pavel Astakhov <pastakhov@yandex.ru>
 * @licence GNU General Public Licence 2.0 or later
 */
class outPrint implements iRawOutput {
	public $returnValue=null;
	private $contents;
	private $raw;
	private $element;
	private $attribs;

	public function __construct( $returnValue, $contents, $raw=false, $element='pre', $attribs = array() ) {
		$this->returnValue = $returnValue;
		$this->raw = $raw;
		$this->contents = (string)$contents;
		$this->element = $element;
		$this->attribs = $attribs;
	}

	public function __toString() {
		$contents = $this->raw ? $this->contents : strtr( $this->contents, array('&'=>'&amp;', '<'=>'&lt;') );
		return is_string( $this->element ) ? "<{$this->element}>$contents</{$this->element}>\n" : "$contents\n";
	}
}
