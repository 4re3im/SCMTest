<!-- TODO: FOR DELETION (ANZGO-3872) -->
<!-- ANZGO-3872 tagged by scamus 20181010 -->
<script> var download_file_url = "<?php echo $this->url('/go/titles') . 'downloadFile/' . $id; ?>"</script>
<div class="modal-header">
    <h4 id="generalModalLabel" class="modal-title">File download</h4>
</div>
<div class="modal-body">
    <div style="padding:5px;font-family:verdana">
        <p style="font-size:14px">Your download will begin automatically in 5 seconds.</p>
        <p style="font-size:14px"><strong>Please note</strong> that you will not be prompted where to save the file. Your files will save to your downloads folder or desktop.</p>
    </div>
    <div style="font-size:12px;margin-bottom:10px;text-align:left">

        <?php

            echo $file_name;

            if($file_size>0) echo " (" .number_format($filesize/1024/1024,1)."mb)";

        ?>

    </div>
</div>

<div class="modal-footer">
    <div class="counter"><input type="text" size="2" name="d2" id="d2" class="d2" readonly style="border:1px;font-size:14px;background-color:#F8FEF0;margin:0px;padding:0px;text-align:right" > seconds remaining.. </div>
    <button id="close_window" class="close_window btn btn-success pull-right" data-dismiss="modal"  type="button">Close</button>
</div>
