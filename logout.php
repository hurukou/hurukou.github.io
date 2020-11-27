<?php

require('function.php');

session_destroy();

header("Location:login_form.php");
