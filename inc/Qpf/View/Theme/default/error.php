<?php 

$this->title = 'Error : '.$e->getMessage(); 

$data = array(
	"code"		=> $e->getCode()
	,"message"	=> $this->Html->b($e->getMessage())
	,"fichier"	=> $e->getFile().":".$e->getLine()
	,"trace"	=> $e->getTraceAsString()
);

echo $this->Html->pre(
	"Une erreur est survenue : "
	.$this->Html->dl($data)
);