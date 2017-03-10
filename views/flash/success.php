<?php if( ! isset( $close ) ){ $close = TRUE; }else{ $close = (bool) $close; } ?>
<div class="alert alert-success">
    <?php if( $close ){ ?><button type="button" class="close" data-dismiss="alert">&times;</button><?php } ?>
    <strong class="margin-right">Success</strong> <?php echo $text; ?>
</div>