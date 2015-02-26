<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Simple Computer</title>
    <link href="http://avallac.academ.org/VM_Static/css/style.css" rel="stylesheet" type="text/css">
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
        <div id="logo"><img src="http://avallac.academ.org/VM_Static/images/logo.gif" alt=""></div>
        <div id="company_name">Simple Computer</div>
        <img src="http://avallac.academ.org/VM_Static/images/header.jpg" alt="" width="720" height="273"></div>

    <div id="menu">
        <table border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td><a href="#" onclick="return reset();">Перезагрузка</a></td>

            </tr>
        </table> 
    </div>

    <div id="body">
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td class="right-block"><p><strong>Accumulator:</strong></p><div id="acc"></div></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                    </tr>
                </table>
            </td><td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td class="right-block"><p><strong>InstructionCounter:</strong></p><div id="instructionCounter"></div> </td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                    </tr>
                </table>
            </td></tr><tr><td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td class="right-block"><p><strong>Operation:</strong></p> <div id="command"></div> </td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                    </tr>
                </table>
            </td><td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-top.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td class="right-block"><p><strong>Flags:</strong></p> <div id="flags"></div></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/right-block-bottom.gif" alt="" width="200" height="8"></td>
                    </tr>
                    <tr>
                        <td><img src="http://avallac.academ.org/VM_Static/images/spacer.gif" alt="" width="1" height="9"></td>
                    </tr>
                </table>
            </td>
            </tr>
            <tr valign="top">
                <td class="body_txt" colspan="2">
                    <table style="border-collapse: collapse; border: 1px solid black; width: 100%;" border="1" cellpadding="5">
                        <tr><td></td>
                    <?php
                    foreach (range(0, 15) as $number) {
                         echo '<td class="memoryTd" bgcolor="#cococo">' . base_convert($number,10,16) . '</td>';
                    }
                    foreach (range(0, \System\Memory::MAX) as $number) {
                        if ($number%16 == 0) {
                            echo '</tr><tr><td class="memoryTd" bgcolor="#cococo" width="0">'.base_convert((int)($number/16), 10, 16).'</td>';
                        }
                        echo '<td class="memoryTd edit" id="m'.$number.'">0</td>';
                    }
                    ?>
                        </tr>
                    </table>
                </td>
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
            <tr>
                <td colspan="2">
                    <textarea rows="20" style="width: 100%;" id="prog">
00 CPUID 00  взять cpu
01 SHR   03  сдвиг на 3
02 SUB   20  вычитаем 1
03 STORE 23  запоминаем в 23
04 MUL   22  умножаем на шаг
05 ADD   21  добавляем сдвиг
06 STORE 24  сохраняем полученный адрес в 23
07 ADD   25  добавляем код store
08 STORE 11  запоминаем в 12
09 LOAD  24  грузим адрес подпрограммы
10 ADD   26  добавляем код load
11 = 0       автоматом сгенерированная команда
12 HALT  00
20 = 1
21 = 48      сдвиг
22 = 32      шаг
23 = 0       ID
25 = 2688    STORE
26 = 2560    LOAD</textarea><input id="make" type="button" style="width: 100%;" value="Запустить" onclick="return sendProgram()">
                </td>
            </tr>
        </table>
    </div>


50 CPUID 00
51 _AND  04
52 JZ    16
53 JUMP  32
54 = 7
16 LOAD  26
17 STORE 28
18 SUB   27
19 JZ    25
20 STORE 26
21 MUL   28
22 STORE 28
23 LOAD  26
24 JUMP  18
25 HALT  00
26 = 5
27 = 2
28 = 0
32 LOAD  42
33 STORE 44
34 SUB   43
35 JZ    41
36 STORE 42
37 MUL   44
38 STORE 44
39 LOAD  42
40 JUMP  34
41 HALT  00
42 = 5
43 = 2
44 = 0
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
                    $('#instructionCounter').html("");
                    VM['instructionCounter'].forEach(function(e, i) {
                        $('#m'+e).addClass('yellowTd');
                        $('#instructionCounter').append("CPU"+i+": "+e+"<br>");
                    });
                    var m = ['instructionCounter', 'acc'];
                    m.forEach(function(key) {
                        $('#'+key).html("");
                        VM[key].forEach(function (e, i) {
                            $('#'+key).append("CPU" + i + ": " + e + "<br>");
                        });
                    });
                    var m = ['command', 'flags'];
                    m.forEach(function(key) {
                        $('#'+key).html("");
                        VM[key].forEach(function (e, i) {
                            $('#'+key).append("CPU" + i + ": <b>" + e + "</b><br>");
                        });
                    });
                    printConsole(buff);
                }
            });
        }, seconds * 1000)
    });

    function printConsole(buff)
    {
        var i = 0;
        var output = $('#taag_output_text');
        output.html('');
        for(i = 0; i < 6; i++) {
            output.append(buff[i] + "\n");
        }
    }

    function reset()
    {
        $.ajax({
            url: 'reset'
        });
        return false;
    }

    function sendProgram()
    {
        $.ajax({
            url: 'loadProgram',
            type: "POST",
            data: "prog="+JSON.stringify($('#prog').val())
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