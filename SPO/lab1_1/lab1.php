<?php

    include("pChart/pData.class");
    include("pChart/pChart.class");

    $DataSet = new pData;

    $ret = [];
    $ser = [];
    $max = 0;
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
    } else {
        $error = 0.001;
    }

    foreach (range(0, 16) as $step) {
        $i = pow(2, $step) * 8;
        $p = $error;
        $s = 20 * 8 + 20 * 8;
        $N0 = $s + $i;
        $Ni = $N0 * pow((1 - $p), $N0);
        $res = $Ni / $N0 * ($i / ($i + $s));
        $ret[$p][] = $res;
        $ser[] = $i;
        $max = max($max, $res);
    }

    $DataSet->AddPoint($ret[$error], "Serie1");
    $DataSet->AddPoint($ser, "Serie3");
    $DataSet->AddSerie("Serie1");
    $DataSet->SetAbsciseLabelSerie("Serie3");
    $DataSet->SetSerieName($p,"Serie1");

    // Initialise the graph
    $chart = new pChart(900,630);
    $chart->setFixedScale(0, $max * 1.05);
    $chart->setFontProperties("Fonts/tahoma.ttf",8);
    $chart->setGraphArea(50,30,785,600);
    $chart->drawFilledRoundedRectangle(7,7,893,623,5,240,240,240);
    $chart->drawRoundedRectangle(5,5,695,225,5,230,230,230);
    $chart->drawGraphArea(255,255,255,TRUE);
    $chart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $chart->drawGrid(4,TRUE,230,230,230,50);

    // Draw the cubic curve graph
    $chart->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());

    // Finish the graph
    $chart->setFontProperties("Fonts/tahoma.ttf",8);
    $chart->drawLegend(800,30,$DataSet->GetDataDescription(),255,255,255);
    $chart->setFontProperties("Fonts/tahoma.ttf",10);
    $chart->drawTitle(50,22,"Эффективность передачи информации",50,50,50,785);
    $chart->Stroke();
