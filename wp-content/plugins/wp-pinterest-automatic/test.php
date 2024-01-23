<?php 

$details = '%09Exp.';

$ecn =  urldecode($details);

//replace tab
$ecn = str_replace("\t" , '' , $ecn);

echo $ecn;

?>