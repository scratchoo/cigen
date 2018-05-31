<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<title><?php echo isset($page_title) ? $page_title : ''; ?></title>
        <meta name="<?php echo $this->security->get_csrf_token_name(); ?>" content="<?php echo $this->security->get_csrf_hash(); ?>">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo site_url('/assets/stylesheets/application.css'); ?>">
    </head>

    <body>

        <?php $this->load->view($content); ?>

        <script
			  src="https://code.jquery.com/jquery-3.2.1.min.js"
			  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
			  crossorigin="anonymous"></script>

        <script type="text/javascript" src="<?php echo base_url("assets/javascripts/ujs.js"); ?>"></script>

    </body>

</html>
