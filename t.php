<?php
$a = array('a'=>1, 'b'=>2, 'c'=>false, 'd'=>0);
$b = array_filter($a, function($v){return $v !== 0;});
var_dump($b);
