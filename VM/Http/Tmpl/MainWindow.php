<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Simple Computer</title>
    <link href="/VM_Static/css/style.css" rel="stylesheet" type="text/css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="http://www.appelsiini.net/projects/jeditable/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>
    <style type="text/css">

        form { padding: 0px; margin: 0px; }
        :focus { outline: 0; }
        input.cmdline { border: none; border: 0px; font-size: 12px; font-family: monospace; padding: 0px; margin:0px; width:100%; }
        table.inputtable { width:100%; vertical-align:top; }
        td.inputtd { width:100%; }
        #input { margin-left: 8px; color: #666; overflow: hidden; }
        #output{ margin-left: 8px; margin-top: 8px; max-width: 540px;}
    </style>
</head>
<body>
<div id="page">
    <div id="header">
        <div id="logo"><img src="/VM_Static/images/logo.gif" alt=""></div>
        <div id="company_name">Simple Computer</div>
        <img src="/VM_Static/images/header.jpg" alt="" width="720" height="273"></div>

    <div id="menu">
        <table border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td><a href="#" onclick="return reset();">Перезагрузка</a></td>

            </tr>
        </table> 
    </div>

    <div id="body">
        <table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr valign="top">
                <td class="body_txt">
                    <table style="border-collapse: collapse; border: 1px solid black;" border="1" cellpadding="5">
                        <tr>
                    <?php
                    foreach (range(0, \System\Memory::MAX) as $number) {
                        if (($number%16 == 0) && $number) {
                            echo "</tr><tr>";
                        }
                        echo '<td class="memoryTd edit" id="m'.$number.'">0</td>';
                    }
                    ?>
                        </tr>
                    </table>
                </td>
                <td width="200">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><img src="/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td class="right-block"><p><strong>Accumulator:</strong></p><div id="acc"></div></td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                        </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><img src="/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td class="right-block"><p><strong>InstructionCounter:</strong></p><div id="instructionCounter"></div> </td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                        </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><img src="/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td class="right-block"><p><strong>Operation:</strong></p> <div id="ticks"></div> </td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                        </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><img src="/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td class="right-block"><p><strong>Flags:</strong></p> O E V M </td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                        </tr>
                        <tr>
                            <td><img src="/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                        </tr>
                    </table>
                   </td>
            </tr>
            <tr>
            <td colspan="2">
                <div id="output">
<pre id="taag_output_text" style="float:left;margin-top:15px;margin-bottom:15px;" class="fig">

</pre><div style="clear:both"></div></div>
                </div>
                <div id="input" style="display: block;">
                    <form name="f" onsubmit="sendCmd(); return false;" class="cmdline" action="">
                        <table class="inputtable"><tbody>
                            <tr>
                                <td><div id="prompt" class="less">guest@sc&gt;&nbsp;</div></td>
                                <td class="inputtd"><input id="inputfield" name="q" type="text" class="cmdline" autocomplete="off" value="" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);"></td>
                            </tr></tbody>
                        </table>
                    </form>
                </div>
            </td>
            </tr>
        </table>
    </div>

    <div id="footer">
        <div class="bottom_menu"><a href="http://avallac.academ.org">Home Page</a>  |  <a href="https://github.com/avallac/Sibsutis/tree/master/VM"">GitHub</a></div>
        <div class="bottom_addr"></div>
    </div>
</div>
</body>
</html>




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
    .yellowTd {
        background-color: yellow;
    }

</style>

<input type="button" id="reset">
<script>
    $(document).ready(function() {
        var seconds = 0.5;
        var buff = new Array();

        $('.edit').editable('changeMemory', {
            cssclass : 'memoryCell'
        });


        setInterval(function(){
            $.ajax({
                url: 'load',
                success: function($data) {
                    $('.redTd').removeClass('redTd');
                    $('.yellowTd').removeClass('yellowTd');
                    var VM = JSON.parse($data);
                    VM['memory'].forEach(function(e, i){
                        var edit = $('#m'+i).children('.memoryCell').length;
                        if ($('#m'+i).html() != e && !edit) {
                            $('#m' + i).html(e);
                            $('#m' + i).addClass('redTd');
                        }
                    });
                    VM['console'].forEach(function(e, i) {
                        buff[i] = e;
                    });
                    $('#m'+VM['instructionCounter']).addClass('yellowTd')
                    $('#instructionCounter').html(VM['instructionCounter']);
                    $('#ticks').html(VM['ticks']);
                    $('#acc').html(VM['acc']);
                    printConsole(buff);
                }
            });
        }, seconds * 1000)
    });

    function printConsole(buff)
    {
        var i=0;
        var output = $('#taag_output_text');
        output.html('');
        for(i = 0; i < 6; i++) {
            output.append(buff[i] + "\n");
        }
    }

    function reset()
    {
        $.ajax({
            url: 'reset',
        });
        return false;
    }
    function sendCmd()
    {
        $.post( "cmd", { 'cmd': $('#inputfield').val() } );
        $('#inputfield').val('');
        return false;
    }

</script>