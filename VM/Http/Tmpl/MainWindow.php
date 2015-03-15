<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Simple Computer</title>
    <link href="http://avallac.academ.org/VM_Static/css/style.css" rel="stylesheet" type="text/css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="http://avallac.academ.org/VM_Static/jquery_jeditable/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>
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
                    foreach (range(0, 9) as $number) {
                         echo '<td class="memoryTd" bgcolor="#cococo">' . base_convert($number,10,16) . '</td>';
                    }
                    foreach (range(0, \System\Memory::MAX) as $number) {
                        if ($number%10 == 0) {
                            echo '</tr><tr><td class="memoryTd" bgcolor="#cococo" width="0">'.(int)($number/10).'</td>';
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

<textarea rows="20" style="width: 100%;" id="prog">00 READ 02
01 JUMP 120
02 = 5 задание
05 MUL 42 умножаем на шаг
06 STORE 44 сохраняем полученный адрес в 34
07 LOAD 44 Грузим сдвиг
08 ADD 46 добавляем код store
09 ADD 45 добавляем счетчик созданных команд
10 STORE 14 запоминаем в 14
11 LOAD 50 грузим команду
12 JZ 22 выход из цикла, если команды кончились
13 ADD 44 добавляем код load
14 = 0 автоматом сгенерированная команда
15 LOAD 11
16 ADD 40   Сдвигаем адрес копируемой команды
17 STORE 11
18 LOAD 45
19 ADD 40   Увеличиваем счетчик созданных программ
20 STORE 45
21 JUMP 7   Цикл командам
22 LOAD 46
23 ADD 45
24 ADD 44
25 STORE 32
26 ADD 40
27 STORE 30
28 ADD 40
29 LOAD 127
30 = 0 Автоматическая команда
31 LOAD 02
32 = 0 Автоматическая команда
33 SUB 40
34 STORE 02
35 JUMP 100
36 JUMP 04  Цикл по ядрам
39 JUMP 64
40 = 1 просто 1
41 = 50 сдвиг
42 = 20 шаг
43 = 0 CPUs
44 = 0 полученный сдвиг
45 = 0 счетчик созданных команд
46 STORE 50
47 = 10 просто 10
49 JZ 50
50 LOAD  61
51 STORE 63
52 SUB   62
53 JZ    60
54 JNEG  60
55 STORE 61
56 MUL   63
57 STORE 63
58 LOAD  61
59 JUMP  52
60 HALT 0
64 LOAD 63
65 MUL 83
66 STORE 2
67 WRITE 2
68 HALT 0
90 LOAD 02
91 STORE 61
92 LOAD 127
93 STORE 62
94 LOAD 39
95 STORE 60
96 JUMP 50
100 LOAD 49
101 ADD 44
102 STORE 106
103 CPUID 00
104 _AND 126 умножаем на битную матрицу
105 SUB 43
106 JZ 0
107 LOAD 43
108 SUB 40
109 JZ 90    Если ядра кончились выходим
110 STORE 43 Уменьшаем счетчик ядер
111 LOAD 11
112 SUB 45   Обнуляем стартовый адрес копируемых команд
113 STORE 11
114 LOAD 45
115 SUB 45   Обнуляем счетчик команд
116 STORE 45
117 JUMP 29
120 CPUID 00 взять cpu
121 SHR 03 сдвиг на 3
122 STORE 127 запоминаем в CPUs
123 SUB 40 вычитаем 1
124 STORE 43 запоминаем в CPU ID
125 JUMP 05
126 = 7</textarea><input id="make" type="button" style="width: 100%;" value="Запустить" onclick="return sendProgram()">
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
        width: 55px;
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
            loadurl  : 'getMemory',
            loadtype: 'POST',
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
                        if ($('#m'+i).html() != '<nobr>'+e+'</nobr>' && !edit) {
                            $('#m' + i).html('<nobr>'+e+'</nobr>');
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