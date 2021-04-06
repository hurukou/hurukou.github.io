<?php

if(!empty($_SESSION['login_time'])){
    if(($_SESSION['login_time'] + $_SESSION['login_ex']) < time()){
        
        session_destroy();
        
        header("Location:login.php");
    }else{
        $_SESSION['login_time'] = time();
        
        if(basename($_SERVER['PHP_SELF']) === 'login_form.php'){
            header("Location:inde.php");
        }
    }
}else{
    if(basename($_SERVER['PHP_SELF']) !== 'login_form.php'){
        header("Location:login_form.php");
    }
}
