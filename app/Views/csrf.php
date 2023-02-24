<?php

use Tischmann\Atlantis\CSRF;

list($key, $value) = CSRF::set();

?>
<input type="hidden" name="<?= $key ?>" value="<?= $value ?>" />