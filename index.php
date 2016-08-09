<?php

function __autoload($className) {
  $path = 'classes/' . $className . '.php';
  
  if (file_exists($path)) {
    require_once $path;
  } else {
    return false;
  }
}

//$singleItem = new GetSingleItem('production');
//$singleItem->printResult(391525898489);


$items = array(
    371678434341,
    391525898489
);

$singleItem = new GetMultipleItems('production');
$singleItem->printResult($items);

?>