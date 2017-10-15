<?php
/*
  Loaded After Laravel Bootstrap in index.php

  functions created here can be used globally
*/

function genUniqueHash() {
  return sha1(microtime(true).mt_rand(10000,90000));
}
?>
