<?php  //Updated for use with Certificate v3+ Chardelle Busch

class block_verify_certificate extends block_base {

    function init() {
        $this->title = get_string('title', 'block_verify_certificate');
    }

    function applicable_formats() {
        return array('all' => true);
    }
    function get_content() {

    if ($this->content !== NULL) {
        return $this->content;
    }

    $this->content = new stdClass;
	$this->content->text = '<p>'.get_string('entercode', 'certificate').'</p>';
	$url = new moodle_url('/blocks/verify_certificate/index.php');
	$this->content->text .= '<center><form class="loginform" name="cert" method="post" action="'. $url . '">';

	$this->content->text .= '<input type="text" name="certnumber" id=name="certnumber" size="10" value="" />';
	$this->content->text .= '<input type="submit" value="'.get_string('validate', 'certificate').'"/></form>';
    $this->content->text .= '<center>';
    $this->content->footer = '';

    return $this->content;
}
function instance_allow_config() {
    return false;
}

}
?>