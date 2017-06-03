<?php

    include("functions.php");

    include("views/header.php");

    if((array_key_exists('page', $_GET)) AND $_GET['page'] == 'timeline'){
        include("views/timeline.php");
    }
    else if((array_key_exists('page', $_GET)) AND $_GET['page'] == 'yourtweets'){
        include("views/yourtweets.php");
    }
    else if((array_key_exists('page', $_GET)) AND $_GET['page'] == 'search'){
        include("views/search.php");
    }
    else if((array_key_exists('page', $_GET)) AND $_GET['page'] == 'publicprofiles'){
        include("views/publicprofiles.php");
    }
    else{
        include("views/home.php");
    }

    include("views/footer.php");

?>