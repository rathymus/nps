<?php

class LinkManager{
	private $list;
	private $conflicts;

	
	public function __construct(){
		$this->list      = array(); //!2d array
		$this->conflicts = array(); //!nd array of all conflicts
	}

	public function add($id, $link){
		$id=strtolower($id);
		
		//check for conflicts
		if(isset($this->list[$id])){
			
			//check tautologies
			if($this->list[$id]!==$link)
				return;

			//register conflict
			if(!isset($this->conflicts[$id]))
				$this->conflicts[$id]=$this->list[$id];
			$this->conflicts[$id][]=$link;
		}

		//conflict or not set $id to the newest $link
		$this->list[$id]=$link;
	}

	public function get($id){
		return $this->list[$id];
	}

	
	public function absorb(LinkManager $ls){
		//Note this ignores ls's conflicts
		foreach($ls->list as $k => $v)
			$this->add($k, $v);
	}

	public function count(){
		return count($this->list);
	}

	public function getConflicts(){
		//with the intention of complaining to the user about them
		return $this->conflicts;
	}
}

?>