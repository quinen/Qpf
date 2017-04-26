<?php 

$this->title = 'Error : '.$e->getMessage(); 

$data = array(
    "code"      => $e->getCode()
    ,"message"  => $this->Html->b($e->getMessage())
    ,"fichier"  => $e->getFile().":".$e->getLine()
    ,"trace"    => $e->getTraceAsString()
    ,'toto'		=> "titi"
);

echo $this->Bs3->panel(array(
		'heading'	=> "Une erreur est survenue : "
		,'body'	=> $this->Bs3->dl($data)
		,'content'	=> "content" // table or list 
		,'footer'	=> "footer"
		,'context'	=> "danger"
	)
);