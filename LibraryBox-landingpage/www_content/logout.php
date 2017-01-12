<?php
    include("globals.php");

    setCookie("sb_auth", "", time() - 3600);
    redirect("/");
?>