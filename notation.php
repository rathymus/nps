<?php

define("NT_LISTDT_RGX", "/ ( ([1-9]+) ([\.]) )|[\*] /x");
define("NT_EM_IT", "--"); // italics
define("NT_EM_BL", "*"); // bold
define("NT_EM_UL", "__"); // underline
define("NT_EM_CO", "---"); // crossed out

class Context{
	//! Type 0 is undefined
	const TYPE_PARAGRAPH = 1;
	const TYPE_QUOTE	 = 2;
	const TYPE_CODE	 = 3;
	const TYPE_LINK	 = 4;

	private $type;	 //! Context type
	private $text;	 //! Raw text
	private $parent;	 //! Parent context
	private $subcx;	 //! Sub-contexts list
	private $meta;	 //! Meta properties list
	private $label;	 //! Meta properties list

	function __construct($raw, $parent){
		$this->parent= $parent;
		$this->text	 = trim($raw);

		if($parent!==NULL){
			parse_type();
			return;
		}

		//Convert newlines to unix-style
		$this->text= str_replace("\n\r", "\n", $s);

		//Set up sub-contexts
		$tmp=explode("\n\n", $subcx);
		for($i=0; $i<count($tmp); $i++)
			array_push($subcx, new Context(trim($tmp[$i]), $this));

	}

	protected function get_meta(){
		return $this->meta;
	}

	protected function get_type(){
		return $this->type;
	}

	protected function get_label(){
		return $this->label;
	}

	/**
	 * Parses context type
	 * It's a good idea to have this be the first method in the parsing stack
	 */
	private function parse_type(){
		switch($this->text[0]){
		case ">":
			$this->type=TYPE_QUOTE;
			break;
		case "``":
			$this->type=TYPE_CODE;
			break;
		default:
			$this->type=TYPE_PARAGRAPH;
		}
		if($this->type!=TYPE_PARAGRAPH)
			//Strip type identifier
			$this->text=ltrim(substr($this->text, 1, strlen($this->text)));
	}

	/**
	 * Parses meta info
	 * Must be called AFTER parse_type() to have the type identifier stripped
	 * Warning: Here be dragons. This function doesn't work as it is and needs
	 * a good paddlin'. Look away.
	 */
	private function parse_meta(){
		if($this->text[0]!=="[")
			return; //no meta definition

		// Get first line
		$m=strtok($this->text, "\n");
		if(strlen($m)>1)
			$m=substr($m, 1, strlen($m));
		else return;

		//Get ID. It should be the first word starting with an alphabetic char.

		$tmp=sscanf("%s", $m);
		if(preg_match("/^[a-z]$/i", $tmp[0]))
			$this->label=strtolower($tmp);

		/*This gets a bit hairy. First we look for a description, which is
		  a string enclosed in double quotes. Only one description is allowed
		  in a meta def. So we'll find 0, 1 or 2 occurrences after checking for
		  escape sequence \. Any more than that is illegal.
		  The description might spill into next lines, in which case we need to
		  concatenate them. A new scope is opened for... well... sanity.
		*/

		$f=strpos($m, "\"");
		if($f===FALSE) //No description
			goto desc_done;
		do{
			$l=strpos($m, "\"", $f);
			if($l===FALSE)
				$m.=strtok($this->text, "\n");
		}while($l===FALSE);

		if($l>$f+1 && ($f!==FALSE || $l!==FALSE))
			//We appear to have hit the jackpot
			$this->meta["desc"]=trim(substr($m, $f+1, $l-1)); //off-by-1?

	desc_done: // Screw good practice. How bad can it be?
	}

	private function parse_meta_get_tkn($s, $in){
		return preg_grep("/ (".preg_quote($s)."[a-zA-Z0-9]+ \s) /", $in);
	}

	private function get_dquotes($in){
		$Q=preg_quote("\"");
		return preg_grep("/ ([^\\]".$Q.")\w([^\\]".$Q.") /", $in);
	}


	protected function parse_em(){
		//$an and $rep must be in lockstep
		$an =array(NT_EM_BL, NT_EM_IT, NT_EM_UL, NT_EM_CO);
		$rep=array("<b>$1</b>", "<i>$1</i>", "<u>$1</u>", "<strike>$1</strike>");

		for($i=0;$i<count($an);$i++){
			$an[$i]=preg_quote($an[$i]);
			$out=preg_replace('/'.$an[$i].'(.+?)'.$an[$i].'/isU',
							  $rep[$i],
							  $this->text);
		}

	}
}

class Label{
	private $id;
	private $ctx;

	public function __construct($identification, $context){
		$this->id  = $identification;
		$this->ctx = $context;
	}

	public function getType(){
		return $this->ctx->getType();
	}

	public function getID(){
		return $this->id;
	}

	public function getContext(){
		return this->ctx;
	}
}

class LabelManager(){
	private static $list=array();

	public static function add($id, $ctx){
		array_push(LabelManager->list, new Label($id, $ctx));
	}
}


function parse($s){
	//first split up text to paragraphs
	$s=str_replace("\n\r", "\n", $s);
	$p = explode("\n\n", $s);
//	echo count($paragraphs);

	//!Main parsing loop. Any hooks should be added here
	for($i=0; $i<count($p); $i++){
		$p[$i]=trim($p[$i]);
		$p[$i]=notation_parse_inline($p[$i]);
		//look for quotes
//		$p[$i]=notation_double_replace("**", array("<b>", "</b>"), $p[$i]);
		//finally wrap it in a paragraph

		$p[$i]="<p>".$p[$i]."</p>";
//	  echo "p is $p[$i]<br>";
	}

	return implode($p);
}

function notation_parse_pref($in){
	$start=trim(sscanf($in, "%s"));

	switch($start){
	case ">": //!quotations
		notation_parse_quotation($in);
		break;
	case "#": //! headers
		break;
	case "|": //! tables
		break;
	case "``": //! preformatted
		break;
	default:  //! Check for lists
		if(preg_match(NT_LISTDT_RGX, $start))
			$in=notation_parse_list($in);
		break;
	}

	return $in;
}

function notation_parse_list($in){
	$l=explode("\n", $in);

	for($i=0; $i<count($l); $i++)
		if(preg_match(NT_LISTDT_RGX, $l[$i])){
			//Remove the prefix (digit and dot or *)
			$l[$i]=preg_replace(NT_LISTDT_RGX, "", $l[$i]);
			//Add list tags
			$l[$i]="<li>".$l[$i]."</li>";
		}else
			$l[$i]=parse($l[$i]);

	return "<ul>".implode($l)"</ul>";
}


/**
 * A meta tag appears after special paragraph annotation and has the format
 * <operator> [meta
 * Most meta info is context-specific, eg. it makes sense for a link to have
 * an "Accessed on <date>" property but not for a table.
 * Nevertheless all meta properties detected are returned by this function
 * in an assoc array.
 */
function notation_parse_meta($mt){

}


function notation_parse_quotation($in){
	$q=">";//preg_quote(">");
	$pos=strpos($in, ">");
	//Quote char must be at the beggining of paragraph
	if($pos===false || $pos>0)
		return $in;
	echo "---found match<br>";
	$in=str_replace("".$q."", "", $in);
	return "<quote>".$in."</quote>";
}

/**
 * Turns > and < into &gt; and &lt;
 * This makes embedded HTML illegal
 *
 */
function notation_replace_ltgt($in){
	$find=array("<", ">");
	$repl=array("&lt;", "&gt;");
	return str_replace($find, $repl, $in);
}

/**
 * Handles annotations such as bold, italics etc
 * @param $cx Context
 * @return $cx with appropriate HTML tags
 */
function notation_parse_em($in){
	//$an and $rep must be in lockstep
	$an =array(NT_EM_BL, NT_EM_IT, NT_EM_UL, NT_EM_CO);
	$rep=array("<b>$1</b>", "<i>$1</i>", "<u>$1</u>", "<strike>$1</strike>");

	for($i=0;$i<count($an);$i++){
		$an[$i]=preg_quote($an[$i]);
		$out=preg_replace('/'.$an[$i].'(.+?)'.$an[$i].'/isU',
						  $rep[$i],
						  $in);
	}


	if($in===$out)
		return $in;
	else
		return notation_parse_em($out);
}

/*
  Replaces everything in $pair with $replace
  @param $pair Two-element array with string to look for
  @param $replace Two-element array with replacement
  @param $in String to operate upon
  @return $in with appropriate replacements
*/
function notation_double_replace($s, $replace, $in){
	$s=preg_quote($s);
//	echo "Before: $in<br>";
	return preg_replace(
		'/\*\*(.+?)\*\*/isU',
		'<b>$1</b>',
		$in);

//	echo "After: $in<br>";
}

$s="ds **hello** asd\n\nAnother paragraph! Isn't that fancy?\n\n > This is a quote";
$s.="\n\n---_*__This is bold, underlined, crossed out and italian!__*_---\n\n";
$s.="This is strong:\n\n**The lazy fox jumped over the brown dog**\n\nThis is bold:\n\n_The lazy fox jumped over the brown dog_\n";
echo "<MATH>&int;_a_^b^{f(x)<over>1+x} dx</MATH>";
echo "</br>**********</br>";

echo parse($s);

?>