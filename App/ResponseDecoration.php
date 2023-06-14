<?php

namespace App;

class ResponseDecoration{
    private $h1;
    private $h2;
    private $p;
    private $lb;
    private $foo;
    private $text;
    private $textLabel;

    function __construct(){
        $this->h1 =          "╔════════════════════╗\n" .
				             "║ [label]\n" .
				             "╠════════════════════╣\n";

        $this->h2 =          "╠════════════════════╣\n" .
				             "║ [label]\n" .
				             "╠════════════════════╣\n";

        $this->p =           " 『[label]』\n";

        $this->lb =          "╠════════════════════╣\n";

        $this->foo =         "╚════════════════════╝";

        $this->text =        "[label]";
        
        $this->textLabel =   "╠═ [label]\n";
    }
    
    private function dictionary($decoration, $replace){
        return str_replace("[label]", $replace, $this->$decoration);
    }

    function decorationResponse($datas){
        $result = "";
        foreach($datas as $data){
            $result .= $this->dictionary($data[0], $data[1]);
        }
        return $result;
    }
}