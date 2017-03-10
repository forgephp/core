<?php echo View::factory('migrations/_header' ); ?>

<?php $message = Session::instance()->get_once('message',false); ?>

<?php if ($message) { ?> 
  <div class="message"><?php echo $message?></div>
<?php } ?>

<div>
	<?php echo HTML::anchor( '/migrations/new' , 'Generate NEW migration') ?>
</div>
<br>

<div>
  <?php echo HTML::anchor( '/migrations/migrate' , 'RUN ALL PENDING MIGRATIONS') ?>
</div>

<div>
  <?php echo HTML::anchor( '/migrations/rollback' , 'ROLLBACK') ?>
</div>

<h3>List of migrations</h3>

<table>
    <thead>
        <tr>
            <th>Migration</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $migrations as $key => $migration ){ ?>
            <tr>
                <td><?php echo basename($migration, EXT); ?></td> 
                <td> 
                    <?php if( array_key_exists( substr( basename( $migration, EXT ), 0, 14 ) , $migrations_runned ) ){ ?>
                        <span class="ok">OK</span>
                    <?php }else{ ?>
                        <span class="pending">PENDING</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<br>

<div>
	<?php echo HTML::anchor( '/migrations/new', 'Generate NEW migration' ) ?>
</div>

<?php echo View::factory('migrations/_footer' ); ?>