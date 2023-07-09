<?php if(isset($errorMessage)) : ?>
    <div id="errorMsg" role="alert" class="msg">
        <span></span>
        <span><?php echo $errorMessage; ?></span>
        <span onclick="deleteMsg('error')" ontouchstart="deleteMsg('error')" class="closeMsg">X</span>
    </div>
<?php endif;
if(isset($infoMessage)) : ?>
    <div id="infoMsg" class="msg">
        <span></span>
        <span><?php echo $infoMessage; ?></span>
        <span onclick="deleteMsg('info')" ontouchstart="deleteMsg('info')" class="closeMsg">X</span>
    </div>
<?php endif; ?>