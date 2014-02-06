<?php
require_once("../../config.php");

global $DB;

    $code = required_param('certnumber', PARAM_ALPHANUM);   // certificate code to verify

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

    // Print results

    echo $OUTPUT->box_start('generalbox boxaligncenter');
    if (!$issues = $DB->get_records('certificate_issues', array('code' => $code))) {
        echo get_string('notfound', 'block_verify_certificate');
    } else {
        echo "<a title=\""; print_string('printerfriendly', 'certificate');
        echo "\" href=\"#\" onclick=\"window.print ()\"><div class=\"printicon\">";
        echo "<img src=\"print.gif\" height=\"16\" width=\"16\" border=\"0\"></img></a></div>";
        /// Print Section
        foreach ($issues as $issue) {
            if (!$certificate = $DB->get_record('certificate', array('id'=> $issue->certificateid))) {
                print_error('course module is incorrect');
            }
            if (!$course = $DB->get_record('course', array('id'=> $certificate->course))) {
                print_error('course is misconfigured');
            }
            if (!$user = $DB->get_record('user', array('id'=> $issue->userid))) {
                print_error('user is unreachable');
            }
            $certificatedate = userdate($issue->timecreated);
            echo '<p>' . get_string('certificate', 'block_verify_certificate') . ' ' . $issue->code . '</p>';
            echo '<p><b>' . get_string('to', 'block_verify_certificate') . ': </b>' . fullname($user) . '<br />';
            echo '<p><b>' . get_string('course', 'block_verify_certificate') . ': </b>' . $course->fullname . '<br />';
            echo '<p><b>' . get_string('date', 'block_verify_certificate') . ': </b>' . $certificatedate . '<br /></p>';
            if ($certificate->customtext !== '') {
                echo '<p><b>' . get_string('customtext', 'block_verify_certificate') . ': </b><br/></p>';
                echo $certificate->customtext;
            }
        }
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
