<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('edit_tf_question1');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	if ($_POST['tid']) {
		header('Location: questions.php?tid='.$_POST['tid']);			
	} else {
		header('Location: question_db.php');
	}
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['question'] = trim($_POST['question']);

	if ($_POST['question'] == ''){
		$msg->addError('QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback']    = $addslashes(trim($_POST['feedback']));
		$_POST['question']    = $addslashes($_POST['question']);
		$_POST['qid']	      = intval($_POST['qid']);
		$_POST['category_id'] = intval($_POST['category_id']);
		$_POST['answer']      = intval($_POST['answer']);
		$_POST['properties']  = $addslashes($_POST['properties']);


		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET	category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			properties='$_POST[properties]',
			answer_0={$_POST[answer]}
			WHERE question_id=$_POST[qid] AND course_id=$_SESSION[course_id]";

		$result	= mysql_query($sql, $db);
		
		$msg->addFeedback('QUESTION_UPDATED');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid']);			
		} else {
			header('Location: question_db.php');
		}
		exit;
	}
}
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

if ($_REQUEST['tid']) {
	echo '<h3><img src="images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid='.$_REQUEST['tid'].'">'._AT('questions').'</a></h3><br />';
} else {
	echo '<h3><img src="images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/question_db.php">'._AT('question_database').'</a></h3><br />';
}

if (!$_POST['submit']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=2";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		$msg->printErrors('QUESTION_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
}

if ($_POST['required'] == 1) {
	$req_yes = ' checked="checked"';
} else {
	$req_no  = ' checked="checked"';
}

if ($_POST['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
	$align_vert = ' checked="checked"';
} else {
	$align_hor  = ' checked="checked"';
}

if ($_POST['answer'] == '') {
	if ($_POST['answer_0'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if ($_POST['answer_0'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer_0'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
} else {
	if ($_POST['answer'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if($_POST['answer'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
}

print_errors($errors);

?>
<form action="tools/tests/edit_question_tf.php" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
<input type="hidden" name="required" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php echo _AT('edit_tf_question1'); ?></th>
</tr>

<tr>
	<td class="row1" align="right" valign="top"><label for="cats"><b><?php echo _AT('category'); ?>:</b></label></td>
	<td class="row1">
		<select name="category_id" id="cats">
		<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top">
			<label for="feedback"><strong><?php echo _AT('optional_feedback'); ?>:</strong></label>
			<br /><a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=feedback','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>
		</td>
		<td class="row1"><textarea id="feedback" cols="50" rows="3" name="feedback" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea></td>
	</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top">
			<label for="question"><strong><?php echo _AT('statement'); ?>:</strong></label>
			<br />
			<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=question','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>	
		</td>
		<td class="row1"><textarea id="question" cols="50" rows="6" name="question" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
	</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="cats"><b><?php echo _AT('option_alignment'); ?>:</b></label></td>
	<td class="row1">
		<label><input type="radio" name="properties" value="5"<?php echo $align_vert; ?> /><?php echo _AT('vertical'); ?></label>
		<label><input type="radio" name="properties" value="6" <?php echo $align_hor;  ?> /><?php echo _AT('horizontal'); ?></label>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('answer'); ?>:</b></td>
	<td class="row1">
	<input type="radio" name="answer" value="1" id="answer1"<?php echo $ans_yes; ?> /><label for="answer1"><?php echo _AT('true'); ?></label>, 
	<input type="radio" name="answer" value="2" id="answer2"<?php echo $ans_no; ?> /><label for="answer2"><?php echo _AT('false'); ?></label>

	</td>
</tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('save'); ?> Alt-s" class="button" name="submit" accesskey="s"/> - <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
</tr>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
