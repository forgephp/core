<h1>Working Jobs</h1>

<?php foreach( $jobs as $job ) { ?>
    <?php echo View::factory( 'Queue/Job', array( 'job' => $job ) )->render() ?>
<?php } ?>

<?php if( sizeof( $jobs ) < 1 )  { ?>
<p>Nothing to work, I'm bored :(</p>
<?php } ?>