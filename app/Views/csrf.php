<?php

$csrf = csrf_set();

?>
<input type="hidden" name="<?= $csrf->key ?>" value="<?= $csrf->token ?>" />