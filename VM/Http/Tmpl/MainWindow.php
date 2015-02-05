<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="http://www.appelsiini.net/projects/jeditable/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>

<style type="text/css">
    .memoryCell input {
        width: 25px;
    }
    .memoryTd {
        width: 30px;
        height: 30px;
        padding: 0;
        text-align: center;
    }
    .redTd {
        background-color: red;
    }
</style>

<table style="border-collapse: collapse; border: 1px solid black;" border="1" cellpadding="5">
    <tr>
<?php
foreach (range(0, \VM\Memory::MAX) as $number) {
    if (($number%20 == 0) && $number) {
        echo "</tr><tr>";
    }
    echo '<td class="memoryTd edit" id="m'.$number.'">0</td>';
}
?>
    </tr>
</table>
<input type="button" id="reset">
<script>
    $(document).ready(function() {
        var seconds = 2;

        $('.edit').editable('changeMemory', {
            cssclass : 'memoryCell'
        });

        $('#reset').click(function() {
            $.ajax({
                url: 'reset',
                success: function() { location.reload(); }
            });
        });

        setInterval(function(){
            $.ajax({
                url: 'load',
                success: function($data) {
                    $('.redTd').removeClass('redTd');
                    var VM = JSON.parse($data);
                    VM['memory'].forEach(function(e, i){
                        var edit = $('#m'+i).children('.memoryCell').length;
                        if ($('#m'+i).html() != e && !edit) {
                            $('#m' + i).html(e);
                            $('#m' + i).addClass('redTd');
                        }
                    })
                }
            });
        }, seconds * 1000)
    });

</script>