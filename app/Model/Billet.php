<?php

namespace App\Model;

class Billet extends Model {
    
    // Renvoie la liste des billets du blog
    public function getBillets() {
        $sql = 'select BIL_ID as id, BIL_DATE as date,'
        . ' BIL_TITRE as titre, BIL_CONTENU as contenu from T_BILLET'
        . ' order by BIL_ID desc';

        //F::log($this->dbTables());

        $billets = $this->query($sql);
        return $billets;
    }    

}