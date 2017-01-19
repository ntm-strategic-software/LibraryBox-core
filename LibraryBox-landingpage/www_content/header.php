<?php

$user;

if(loggedIn()) {
    $user = getUser()[0];
} else {
    $user = array();
}

?>

<div id="top-nav" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <!--<a href="/content/" class="brand navbar-brand"><img src="img/lbx-logo-small-white.png" width="24" alt="lbx-logo-small">&nbsp;Scatterbox</a>-->
            <a href="/content/" class="brand navbar-brand"><i class="fa fa-cube"></i>&nbsp;Scatterbox</a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span data-l10n-id="commonNavbarToggle" class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/content/" class="active"><i class="fa fa-home"></i> <span data-l10n-id="commonNavbarHome">Home</span></a></li>
                <!--<li><a href="/Shared/text/"><i class="fa fa-file-text-o"></i> <span data-l10n-id="commonNavbarText">Text</span></a></li>
                <li><a href="/Shared/audio/"><i class="fa fa-file-sound-o"></i> <span data-l10n-id="commonNavbarAudio">Audio</span></a></li>
                <li><a href="/Shared/video/"><i class="fa fa-file-video-o"></i> <span data-l10n-id="commonNavbarVideo">Video</span></a></li>
                <li><a href="/Shared/software/"><i class="fa fa-file-image-o"></i> <span data-l10n-id="commonNavbarImages">Images</span></a></li>
                <li><a href="/Shared/software/"><i class="fa fa-file-archive-o"></i> <span data-l10n-id="commonNavbarApps">Apps</span></a></li>-->
                <!--<li><a href="/Shared/"><i class="fa fa-folder-open-o"></i> <span data-l10n-id="commonNavbarAll">All Content</span></a></li>-->
                <!--<li><a data-l10n-id="commonNavbarStats" href="/content/stats.html">Statistics</a></li>-->
                <?php
                    if(loggedIn()) {
                        // print '<li><a href="edituser.php?id=' . $user["id"] . '"><i class="fa fa-cog"></i> <span data-l10n-id="commonNavbarSettings">Settings</span></a></li>';
                        if($user["admin"] === 1) {
                            echo  '<li><a href="/content/users.php"><i class="fa fa-users"></i> <span data-l10n-id="commonNavbarUsers">Users</span></a></li>';
                            echo  '<li><a href="/content/groups.php"><i class="fa fa-gears"></i> <span data-l10n-id="commonNavbarGroups">Groups</span></a></li>';
                        }
                    }
                ?>
                <li><a href="/content/about.php"><i class="fa fa-question-circle"></i> <span data-l10n-id="commonNavbarAbout">About</span></a></li>
                <?php
                    if(loggedIn()) {
                        echo '<li><a href="/content/logout.php"><i class="fa fa-sign-in"></i> <span data-l10n-id="commonNavbarLogout">Logout</span></a></li>';
                    } else {
                        echo '<li><a href="/content/login.php"><i class="fa fa-sign-in"></i> <span data-l10n-id="commonNavbarLogin">Login</span></a></li>';
                    }
                ?>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>
<div style="padding-top: 65px;"></div>