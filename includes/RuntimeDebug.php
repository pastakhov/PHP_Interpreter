<?php
namespace Foxway;
/**
 * Runtime class of Foxway extension.
 *
 * @file Runtime.php
 * @ingroup Foxway
 * @author Pavel Astakhov <pastakhov@yandex.ru>
 * @licence GNU General Public Licence 2.0 or later
 */
class RuntimeDebug extends Runtime {

	private $debug = array();
	private $listParamsDebug = array();
	private $savedListParams;
	private $stackDebug = array();
	private $lastCommandDebug;

	protected function pushStack() {
		parent::pushStack();
		$this->stackDebug[] = $this->listParamsDebug;
	}

	protected function popStack() {
		parent::popStack();

		$this->savedListParams = $this->listParamsDebug;
		if( count($this->stackDebug) == 0 ) {
			$this->listParamsDebug = array();
		} else {
			$this->listParamsDebug = array_pop($this->stackDebug);
		}
	}

	protected function parenthesesClose() {
		parent::parenthesesClose();
		$this->lastCommandDebug = $this->lastCommand;
	}

	public function getDebug() {
		$return = implode('<br>', $this->debug);
		$this->debug = array();
		return $return;
	}

	/**
	 *
	 * @param mixed $operator
	 * @param RVariable $param
	 */
	protected function doOperation($operator, $param = null) {
		if( $operator == '=' ) {
			if( $param instanceof RArray ) {
				$i = $param->getIndex();
				$return = \Html::element( 'span', array('class'=>'foxway_variable'), $param->getParent()->getName() ) .
						( $i instanceof RValue ? '['.self::getHTMLForValue($i).']' : '[]' ) .
						'&nbsp;<b>=></b>&nbsp;=&nbsp;';
			}else{
				$return = \Html::element( 'span', array('class'=>'foxway_variable'), $param->getName() ) .
						'&nbsp;<b>=></b>&nbsp;=&nbsp;';
			}
		} else {
			if( ($operator == T_INC || $operator == T_DEC) && $this->lastOperator ) {
				$v = $this->lastParam->getValue();
				$return = self::getHTMLForValue($this->lastParam) .
					self::getHTMLForOperator($operator) .
					' = (' . self::getHTMLForValue( new RValue($operator == T_INC ? $v+1 : $v-1) ) . ')' .
					'&nbsp;<b>=></b>&nbsp;';
			} else {
				$return = ($param === null ? '' : self::getHTMLForValue($param)) .
						self::getHTMLForOperator($operator) .
						self::getHTMLForValue($this->lastParam) .
						'&nbsp;<b>=></b>&nbsp;';
			}
		}

		parent::doOperation($operator, $param);

		$this->debug[] = $return . self::getHTMLForValue( $this->lastParam );
	}

	public function addOperator($operator) {
		if( $operator == ',' ) {
			$this->doMath();
			$this->listParamsDebug[] = self::getHTMLForValue($this->lastParam);
		}

		$return = parent::addOperator($operator);

		if( $operator == '?' ) {
			$this->debug[] = self::getHTMLForValue($this->lastParam) . "&nbsp;?&nbsp;<b>=></b>&nbsp;" . self::getHTMLForValue( new RValue($return?true:false) );
		}elseif( $operator == ')' ) {
			switch ($this->lastCommandDebug) {
				case false:
				case T_ARRAY:
				case T_ECHO:
					break;
				case T_IF:
					$this->debug[] = self::getHTMLForCommand(T_IF).
							"(&nbsp;" . self::getHTMLForValue( $this->lastParam ) .
							"&nbsp;)&nbsp;<b>=></b>&nbsp;" .
							self::getHTMLForValue( new RValue($this->lastParam->getValue() ? true : false) );
					break;
				default:
					$this->debug[] = self::getHTMLForCommand($this->lastCommandDebug) . "( " . self::getHTMLForValue($this->lastParam) . " )";
					break;
			}
		}
		return $return;
	}

	public function getCommandResult( ) {
		$lastCommand = $this->lastCommand;

		$return = parent::getCommandResult();

		switch ($lastCommand) {
			case T_ECHO:
				$this->debug[] = self::getHTMLForCommand($lastCommand) . '&nbsp;' . implode(', ', $this->savedListParams) . ';';
				$this->debug[] = implode('', $return[1]);
				break;
			default :
				$this->debug[] = is_array($return) ? $return[1] : $return ;
				break;
		}
		return $return;
	}

	/**
	 *
	 * @param RVariable $param
	 * @return string
	 */
	private static function getHTMLForValue( $param) {
		$value = $param->getValue();
		$class = false;
		if( $value === true ) {
			$class = 'foxway_construct';
			$value = 'true';
		}elseif( $value === false ) {
			$class = 'foxway_construct';
			$value = 'false';
		}elseif( $value === null ) {
			$class = 'foxway_construct';
			$value = 'null';
		}elseif( is_string($value) ) {
			$class = 'foxway_string';
			$value = "'$value'";
		}elseif( is_numeric($value) ) {
			$class = 'foxway_number';
		}  elseif( is_array($value) ) {
			if( count($value) <= 3 ) {
				return var_export($value, true);
			} else {
				return 'array';
			}
		}
		if( $class ) {
			$value = \Html::element('span', array('class'=>$class), $value);
		} else {
			$value = strtr( $value, array('&'=>'&amp;', '<'=>'&lt;') );
		}

		if( $param instanceof RArray ) {
			$indexes = array();
			do {
				if( $param->getIndex() === null ) {
					array_unshift( $indexes, '[]' );
				}elseif( $param->is_set() ) {
					array_unshift( $indexes, '[' . self::getHTMLForValue( $param->getIndex() ) . ']' );
				} else {
					array_unshift( $indexes, \Html::element( 'span', array('class'=>'foxway_undefined'), "[" ) .
							self::getHTMLForValue( $param->getIndex() ) .
							\Html::element( 'span', array('class'=>'foxway_undefined'), "]" ) );
				}
				$param = $param->getParent();
			} while ( $param instanceof RArray );
			return ($param->is_set() ? '' : \Html::element( 'span', array('class'=>'foxway_undefined'), "Undefined " ) ) .
					\Html::element( 'span', array('class'=>'foxway_variable'), $param->getName() ) .
					implode('', $indexes) .	"($value)";
		} elseif( $param instanceof RVariable ) {
			return ($param->is_set() ? '' : \Html::element( 'span', array('class'=>'foxway_undefined'), "Undefined " ) ) .
					\Html::element( 'span', array('class'=>'foxway_variable'), $param->getName() ) . "($value)";
		}
		return $value;
	}

	private static function getHTMLForCommand($command) {
		switch ($command) {
			case T_ECHO:
				$return = \Html::element('span', array('class'=>'foxway_construct'), 'echo');
				break;
			case T_IF:
				$return = \Html::element('span', array('class'=>'foxway_construct'), 'if');
				break;
			case T_ARRAY:
				$return = \Html::element('span', array('class'=>'foxway_construct'), 'array');
				break;
			default:
				$return = $command;
				break;
		}
		return $return;
	}

	private static function getHTMLForOperator($operator) {
		switch ($operator) {
			case T_CONCAT_EQUAL:// .=
				$operator = '.=';
				break;
			case T_PLUS_EQUAL:// +=
				$operator = '+=';
				break;
			case T_MINUS_EQUAL:// -=
				$operator = '-=';
				break;
			case T_MUL_EQUAL: // *=
				$operator = '*=';
				break;
			case T_DIV_EQUAL: // /=
				$operator = '/=';
				break;
			case T_MOD_EQUAL: // %=
				$operator = '%=';
				break;
			case T_AND_EQUAL:// &=
				$operator = '&=';
				break;
			case T_OR_EQUAL:// |=
				$operator = '|=';
				break;
			case T_XOR_EQUAL:// ^=
				$operator = '^=';
				break;
			case T_SL_EQUAL:// <<=
				$operator = '<<=';
				break;
			case T_SR_EQUAL:// >>=
				$operator = '>>=';
				break;
			case T_DOUBLE_ARROW:// =>
				$operator = '=>';
				break;
			case T_INC:// ++
				return '++';
				break;
			case T_DEC:// --
				return '--';
				break;
			case T_IS_SMALLER_OR_EQUAL: // <=
				$operator = '<=';
				break;
			case T_IS_GREATER_OR_EQUAL: // >=
				$operator = '>=';
				break;
			case T_IS_EQUAL: // ==
				$operator = '==';
				break;
			case T_IS_NOT_EQUAL: // !=
				$operator = '!=';
				break;
			case T_IS_IDENTICAL: // ===
				$operator = '===';
				break;
			case T_IS_NOT_IDENTICAL: // !==
				$operator = '!==';
				break;
			case T_INT_CAST:
				$operator = \Html::element('span', array('class'=>'foxway_construct'), '(integer)');
				break;
			case T_DOUBLE_CAST:
				$operator = \Html::element('span', array('class'=>'foxway_construct'), '(float)');
				break;
			case T_STRING_CAST:
				$operator = \Html::element('span', array('class'=>'foxway_construct'), '(string)');
				break;
			case T_ARRAY_CAST:
				$operator = \Html::element('span', array('class'=>'foxway_construct'), '(array)');
				break;
			case T_BOOL_CAST:
				$operator = \Html::element('span', array('class'=>'foxway_construct'), '(bool)');
				break;
		}
		return "&nbsp;$operator&nbsp;";
	}

}