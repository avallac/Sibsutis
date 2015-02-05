<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="http://www.appelsiini.net/projects/jeditable/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>

<style type="text/css">
    .editable input[type=submit] {
        color: #F00;
        font-weight: bold;
    }
    .editable input[type=button] {
        color: #0F0;
        font-weight: bold;
    }
    .someclass input {
        width: 25px;
    }

</style>

<table style="border-collapse: collapse; border: 1px solid black;" border="1" cellpadding="5">
    <tr>

<?php
foreach (range(0, \VM\Memory::MAX) as $number) {
    if (($number%20 == 0) && $number) {
        echo "</tr><tr>";
    }
    echo '<td class="edit" id="m'.$number.'" style="width: 30px; height: 30px; padding: 0px; text-align: center; valign: middle; align: center;">'.$this->memory[$number].'</td>';
}
?>
    </tr>
</table>
<input type="button" id="reset">
<script>
    $(document).ready(function() {
        $('.edit').editable('changeMemory', {
            cssclass : 'someclass'
        });
        $('#reset').click(function() {
            $.ajax({
                url: '/reset',
                success: function() { location.reload(); }
            });
        });
    });
</script>