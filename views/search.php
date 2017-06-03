<div class="container mainContainer">
    
    <div class="row">
        <div class="col-md-8">
            <h2>Search Results</h2>
            
            <?php displayTweets('search'); ?>
            
        </div>
        <div class="col-md-4">
        
            <h2>Find a Tweet</h2>
        <?php displaySearch(); ?>
            
        <hr>
            
        <?php displayTweetBox(); ?>
        
        </div>
    </div>
    
</div>