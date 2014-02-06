<?php
require_once("../../config.php");
require_once("$CFG->dirroot/enrol/locallib.php");

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
        echo '<a title="' . get_string('printerfriendly', 'certificate') . '" href="#" onclick="window.print()">';
        echo '<div class="printicon">';
        echo '<img src="print.gif" height="16" width="16" border="0"/></a></div>';
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

            $enrol_manager = new course_enrolment_manager($PAGE, $course);
            $user_enrols = $enrol_manager->get_user_enrolments($user->id);
            $start_date = 0;
            $end_date = 0;
            foreach ($user_enrols as $enrol) {
                if ($enrol->timestart > 0) {
                    $start_date = $enrol->timestart;
                }
                if ($enrol->timeend > 0) {
                    $end_date = $enrol->timeend;
                }
            }
            if (($start_date > 0 and $end_date > 0)) {
                $fmt = '%d/%m/%Y'; // Default format
                if ($certificate->datefmt == 1) {
                    $fmt = '%B %d, %Y';
                    $certificatedate = userdate($ts, '%B %d, %Y') . " a " . userdate($te, '%B %d, %Y');
                } else if ($certificate->datefmt == 2) {
                    $suffix = certificate_get_ordinal_number_suffix(userdate($ts, '%d'));
                    $fmt = '%B %d' . $suffix . ', %Y';
                    $certificatedate = userdate($ts, '%B %d' . $suffix . ', %Y') . " a " . userdate($te, '%B %d' . $suffix . ', %Y');
                } else if ($certificate->datefmt == 3) {
                    $fmt = '%d %B %Y';
                    $certificatedate = userdate($ts, '%d %B %Y') . " a " . userdate($te, '%d %B %Y');
                } else if ($certificate->datefmt == 4) {
                    $fmt = '%B %Y';
                    $certificatedate = userdate($ts, '%B %Y') . " a " . userdate($te, '%B %Y');
                } else if ($certificate->datefmt == 5) {
                    $fmt = get_string('strftimedate', 'langconfig');
                    $certificatedate = userdate($ts, get_string('strftimedate', 'langconfig')) . " a " . userdate($te, get_string('strftimedate', 'langconfig'));
                }
                $start_date = userdate($start_date, $fmt);
                $end_date = userdate($end_date, $fmt);
            } else {
                $start_date = '';
                $end_date = '';
            }

            $certificatedate = userdate($issue->timecreated);
            echo '<p>' . get_string('certificate', 'block_verify_certificate') . " <strong>{$issue->code}</strong></p>";
            echo '<p><strong>' . get_string('to', 'block_verify_certificate') . ': </strong>' . fullname($user) . '</p>';
            echo '<p><strong>' . get_string('course', 'block_verify_certificate') . ": </strong>{$course->fullname}</p>";
            echo '<p><strong>' . get_string('date', 'block_verify_certificate') . ": </strong>$certificatedate</p>";
            echo '<p><strong>' . get_string('enrol_period', 'block_verify_certificate') . ": </strong>$start_date - $end_date</p>";
            if ($certificate->printhours) {
                echo '<p><strong>' . get_string('printhours', 'block_verify_certificate') . ": </strong>{$certificate->printhours}</p>";
            }
            if ($certificate->customtext !== '') {
                echo '<p><strong>' . get_string('customtext', 'block_verify_certificate') . ':</strong></p>';
                echo $certificate->customtext;
            }
        }
    }

    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
