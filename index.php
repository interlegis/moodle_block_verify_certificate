<?php
require_once("../../config.php");

global $DB;

    $id = required_param('certnumber', PARAM_ALPHANUM);   // certificate code to verify

	$PAGE->set_pagelayout('standard');
	$strverify = get_string('verifycertificate', 'block_verify_certificate');
	$PAGE->set_url('/blocks/verify_certificate/index.php', array('certnumber' => $id));
    $context = get_context_instance(CONTEXT_SYSTEM);
    $PAGE->set_context($context);

	/// Print the header

    $PAGE->navbar->add($strverify);
    $PAGE->set_title($strverify);
    $PAGE->set_heading($strverify);
	$PAGE->requires->css('/blocks/verify_certificate/printstyle.css');
    echo $OUTPUT->header();
	$certificate = $DB->get_records_sql("SELECT s.*
                              FROM {certificate_issues} s
                             WHERE s.code = ?", array($id));
	if (! $certificate) {
		echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo get_string('notfound', 'block_verify_certificate');
		echo $OUTPUT->box_end();
    } else {
		echo $OUTPUT->box_start('generalbox boxaligncenter');
		echo "<a title=\""; print_string('printerfriendly', 'certificate');
		echo "\" href=\"#\" onclick=\"window.print ()\"><div class=\"printicon\">";
		echo "<img src=\"print.gif\" height=\"16\" width=\"16\" border=\"0\"></img></a></div>";
	/// Print Section
		foreach ($certificate as $certrecord) {
			$certificatedate = userdate($certrecord->timecreated);
			echo '<p>' . get_string('certificate', 'block_verify_certificate') . ' ' . $certrecord->code . '</p>';
			echo '<p><b>' . get_string('to', 'block_verify_certificate') . ': </b>' . $certrecord->studentname . '<br />';
			echo '<p><b>' . get_string('course', 'block_verify_certificate') . ': </b>' . $certrecord->classname . '<br />';
			echo '<p><b>' . get_string('date', 'block_verify_certificate') . ': </b>' . $certificatedate . '<br /></p>';
			if ($certrecord->reportgrade != null) {
				echo '<p><b>' . get_string('grade', 'block_verify_certificate') . ': </b>' . $certrecord->reportgrade . '<br /></p>';
			}
		}
		echo $OUTPUT->box_end();
	}
    echo $OUTPUT->footer();