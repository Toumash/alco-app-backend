<?php
require_once('bitbucket.lib.php');

// Config Values:
$basicAuth = 'Q1NCdWdSZXBvcnRlcjphbGNvcmVwb3J0MzIx'; // Base64 encode of: username:password of your Read only user.
$bitBucketAccount = 'code-sharks'; // Team account which contains your repo.
$bitBucketRepo = 'alcohol'; // Name of your repo.
$issueComponent = 'Unsorted'; // Component name for this issue. Recommended to use an 'Unsorted' component to flag unprocessed bugs.
$companyName = 'Code Sharks'; // The name of your company or department (used for the confirmation email).


// Process any POST.
if (isset($_POST) && !empty($_POST['bugformtitle']) && !empty($_POST['bugformdescription'])) {
    $status = submitBug($_POST['bugformtitle'], $_POST['bugformdescription'], $_POST['bugformuser'], $bitBucketAccount, $bitBucketRepo, $basicAuth, $issueComponent);
    if ($status === FALSE) {
        echo("<span class='bugformerror'>Sorry, there was an error submitting your bug. Please try again later or contact Support directly.</span>");
    } else {
        echo("<span class='bugformsuccess'>Thank you, your bug <b># " . $status['issueid'] . "</b> has been submitted.</span>");
        sendBugEmail($_POST['bugformuser'], $status['issueid'], $companyName, $status['issueurl']); // Leave URL parameter blank if you don't want it in the email.
    }
}