<?php

$tableau = array();

for ($i=0; $i < 5 ; $i++)
{
  $tableau[$i]['a'] = 'a'.$i;
  $tableau[$i]['b'] = 'b'.$i;
}

foreach ($tableau as $key)
{
  echo $key['a'];
  echo $key['b'];
  echo "<hr>";
}
