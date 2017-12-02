<?php
/*
  Loaded After Laravel Bootstrap in index.php

  functions created here can be used globally
*/

function genUniqueHash($len) {
   return bin2hex(random_bytes($len));
}
?>
