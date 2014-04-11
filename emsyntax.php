<?php


abstract class SyntaxManager{
	private $text;
	private $meta;
	private $link;
	
	public function __construct($in, $ref);
	public function parse();
	
}


class NpsMarkup extends SyntaxManager{
	

	public function __construct($in, $ref){
		$this->p=$in;
	}

	public function parse_markup(){
		//$an and $rep must be in lockstep
		$an =array(
			preg_quote("*"),
			preg_quote("_"),
			preg_quote("__"),
			preg_quote("---")
			);
		$rep=array("<b>$1</b>",
				   "<i>$1</i>",
				   "<u>$1</u>",
				   "<strike>$1</strike>"
			);
		
		$can=count($an);

		for($i=0; $i<$can; $i++){

			$this->p=preg_replace(
				'/'.$an[$i].'(.+?)'.$an[$i].'/isU',
				$rep[$i],
				$this->p);
		}

	}

	public function parse_links(){
		return;
	}

	private function strip_html(){
		$this->p=str_replace("<", "&lt;", $this->p);
		$this->p=str_replace(">", "&gt;", $this->p);
	}
}


class Markdown extends SyntaxManager{
	
	public function __construct($in, $ref){
		$this->p=$in;
	}
}




?>