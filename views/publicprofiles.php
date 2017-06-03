<div class="container mainContainer">
    
    <div class="row">
        <div class="col-md-8">
            
            <?php if ((array_key_exists('userid',$_GET)) AND $_GET['userid']){?>
            
                <h2>Profile</h2>
                <?php displayTweets($_GET['userid']);?>
            
            <?php } else{ ?>
            
                <h2>Active Users</h2>

                <?php displayUsers(); ?>
            
            <?php } ?>
            
        </div>
        <div class="col-md-4">
        
            <h2>Find a Tweet</h2>
        <?php displaySearch(); ?>
            
        <hr>
            
        <?php displayTweetBox(); ?>
        
        </div>
    </div>
    
</div>