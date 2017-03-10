<?php if( ! isset( $close ) ){ $close = TRUE; }else{ $close = (bool) $close; } ?>
<div class="alert alert-block alert-error">
    <?php if( $close ){ ?><button type="button" class="close" data-dismiss="alert">&times;</button><?php } ?>
    <h4>Error!</h4>
    <?php echo $text; ?>
</div>
