<?php
/*
* When resubmit a form, the csrf error show up
*/

// https://stackoverflow.com/questions/42001577/codeigniter-showing-error-when-i-try-to-resubmit-form-with-csrf-protection-set-t
class MY_Security extends CI_Security {

    public function __construct()
    {
        parent::__construct();
    }

    public function csrf_show_error()
    {
        header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), TRUE, 200);
    }
}
