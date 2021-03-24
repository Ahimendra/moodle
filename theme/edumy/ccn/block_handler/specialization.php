<?php
/*
@ccnRef: @block_cocoon/block.php
*/

defined('MOODLE_INTERNAL') || die();

if (!($this->config)) {
    $this->content->text = '<h5 class="mb30">'.$this->title.'</h5>';
    return $this->content->text;
}
