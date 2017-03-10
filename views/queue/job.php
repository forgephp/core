<div class="row well">
    <div class="span12">
        <div class="row">
            <div class="span10">
                <dl class="dl-horizontal">
                    <dt>ID</dt>
                    <dd><?php echo $job->pk() ?></dd>

                    <dt>Queue</dt>
                    <dd><?php echo $job->queue ?></dd>

                    <dt>Attempts</dt>
                    <dd><?php echo $job->attempts ?></dd>

                    <dt>Handler</dt>
                    <dd><code><?php try { echo serialize( $job->handler );  } catch( Exception $e ) { echo "Invalid Handler"; } ?></code></dd>

                    <dt>Run At</dt>
                    <dd><?php echo $job->run_at ?></dd>

                    <dt>Created At</dt>
                    <dd><?php echo $job->created_at ?></dd>

                    <?php if( NULL !== $job->failed_at ) { ?>
                    <dt>Failed At</dt>
                    <dd><?php echo $job->failed_at ?></dd>

                    <dt>Error Backtrace</dt>
                    <dd><pre><code><?php echo $job->error ?></code></pre></dd>
                    <?php } ?>

                    <?php if( NULL !== $job->locked_at ) { ?>
                    <dt>Locked By</dt>
                    <dd><?php echo $job->locked_by ?></dd>

                    <dt>Locked At</dt>
                    <dd><?php echo $job->locked_at ?></dd>
                    <?php } ?>
                </dl>
            </div>

            <div class="span2">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" class="#">
                        <i class="icon-wrench"></i>
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="/<?php echo $route->uri( array( 'action' => 'requeue', 'id' => $job->pk() ) ) ?>"><i class="icon-repeat"></i> Retry</a></li>
                        <li><a href="/<?php echo $route->uri( array( 'action' => 'remove', 'id' => $job->pk() ) ) ?>"><i class="icon-remove"></i> Remove</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>