<h1>Failed Jobs</h1>

<?php foreach( $jobs as $job ) { ?>
    <?php echo View::factory( 'Queue/Job', array( 'job' => $job ) )->render() ?>
<?php } ?>

<?php if( sizeof( $jobs ) < 1 )  { ?>
<p>Hurray! Nothing has crapped the bed... yet!</p>
<?php } ?>