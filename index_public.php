<?php
$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

if (isset($_GET['usr']) && isset($_GET['id'])) {
    $_SESSION['usr'] = $_GET['usr'];
    $_SESSION['pswd'] = $_GET['id'];
}

if(!isset($_SESSION['usr']) || !isset($_SESSION['pswd'])){
 header("Location: signin/");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/lope.ico">

    <title>CWN editr</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Chart.js -->
    <script src="js/Chart.js"></script>

  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">CWN <span class="label label-default">editr</span></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">APIs</a></li>
            <li><a href="#">Help</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo  $_SESSION['usr']; ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Editing history</a></li>
                <li class="divider"></li>
                <li><a href="signin/logout.php">Sign out</a></li>
              </ul>
            </li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#"><span class="glyphicon glyphicon-stats"></span> 總覽 (Overview)</a></li>
            <li><a href="words/index_public.php"><span class="glyphicon glyphicon-font"></span> 單詞 (Words)</a></li>
            <!-- <li><a href="relations/"><span class="glyphicon glyphicon-transfer"></span> 語意關係 (Relations)</a></li> -->
            <!-- <li><a href="synsets/"><span class="glyphicon glyphicon-list"></span> 同義詞集 (Synsets)</a></li> -->
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">總覽 (Overview)</h1>

          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
              <canvas id="lemma" width="150" height="150"></canvas>
              <br />
              <h5><span class="label label-success">cwn2</span> <span id="cwn2_lemma_total">0</span> + <span class="label label-danger">cwn1</span> 10653</h5>
              <h5>= <span id="lemma_total">10653</span> 詞形 (lemmas)</h5>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <canvas id="sense" width="150" height="150"></canvas>
              <br />
              <h5><span class="label label-success">cwn2</span> <span id="cwn2_sense_total">0</span> + <span class="label label-danger">cwn1</span> 28090</h5>
              <h5>= <span id="sense_total">28090</span> 詞義 (senses)</h5>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <canvas id="facet" width="150" height="150"></canvas>
              <br />
              <h5><span class="label label-success">cwn2</span> <span id="cwn2_facet_total">0</span> + <span class="label label-danger">cwn1</span> 6268</h5>
              <h5>= <span id="facet_total">6268</span> 意面 (meaning facets)</h5>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <canvas id="synset" width="150" height="150"></canvas>
              <br />
              <h5><span class="label label-success">cwn2</span> <span id="cwn2_synset_total">0</span> + <span class="label label-danger">cwn1</span> 3986</h5>
              <h5>= <span id="synset_total">3986</span> 同義詞集 (synsets)</h5>
            </div>
          </div>

          <h2 class="sub-header">統計數字 (Stats)</h2>
          <div class="table-responsive">
            <table id="stats" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th></th>
                  <th><span class="label label-success">cwn2</span> 詞義 (sense)</th>
                  <th><span class="label label-danger">cwn1</span> 詞義 (sense)</th>
                  <th><span class="label label-success">cwn2</span> 同義詞集 (synset)</th>
                  <th><span class="label label-danger">cwn1</span> 同義詞集 (synset)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>同義關係 (synonymy)</td>
                  <td>0</td>
                  <td>32326</td>
                  <td>N/A</td>
                  <td>N/A</td>
                </tr>
                <tr>
                  <td>反義關係 (antonymy)</td>
                  <td>0</td>
                  <td>5093</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>上位關係 (hypernymy)</td>
                  <td>0</td>
                  <td>1104</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>下位關係 (hyponym)</td>
                  <td>0</td>
                  <td>548</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>整體關係 (holonymy)</td>
                  <td>0</td>
                  <td>18</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>部分關係 (meronymy)</td>
                  <td>0</td>
                  <td>10</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>近義關係 (near-synonymy)</td>
                  <td>0</td>
                  <td>4907</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>類義關係 (paranymy)</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>附屬關係 (pertainymy)</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>致使關係 (causality)</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>方式關係 (troponymy)</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>異體關係 (variant)</td>
                  <td>0</td>
                  <td>1893</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
      
    $.getJSON("src/stats.php", function(data) {
      
      var cwn2_lemma_total = document.getElementById('cwn2_lemma_total');
      var lemma_total = document.getElementById('lemma_total');

      cwn2_lemma_total.innerHTML = data.lemma_addition;
      lemma_total.innerHTML = data.lemma_addition+10653;

      var cwn2_lemma = 180*data.lemma_addition/(data.lemma_addition+10653);
      var cwn1_lemma = 180-cwn2_lemma;
      var pieData_lemma = [
          {
            value: cwn2_lemma,
            color: "#63AD3E"
          },
          {
            value : cwn1_lemma,
            color : "#B9443C"
          }
          
      ];

      var cwn2_sense_total = document.getElementById('cwn2_sense_total');
      var sense_total = document.getElementById('sense_total');

      cwn2_sense_total.innerHTML = data.sense_addition;
      sense_total.innerHTML = data.sense_addition+28090;

      var cwn2_sense = 180*data.sense_addition/(data.sense_addition+28090);
      var cwn1_sense = 180-cwn2_sense;
      var pieData_sense = [
          {
            value: cwn2_sense,
            color: "#63AD3E"
          },
          {
            value : cwn1_sense,
            color : "#B9443C"
          }
          
      ];

      var cwn2_facet_total = document.getElementById('cwn2_facet_total');
      var facet_total = document.getElementById('facet_total');

      cwn2_facet_total.innerHTML = data.facet_addition;
      facet_total.innerHTML = data.facet_addition+6268;

      var cwn2_facet = 180*data.facet_addition/(data.facet_addition+6268);
      var cwn1_facet = 180-cwn2_facet;
      var pieData_facet = [
          {
            value: cwn2_facet,
            color: "#63AD3E"
          },
          {
            value : cwn1_facet,
            color : "#B9443C"
          }
          
      ];

      var cwn2_synset_total = document.getElementById('cwn2_synset_total');
      var synset_total = document.getElementById('synset_total');

      cwn2_synset_total.innerHTML = data.synset_addition;
      synset_total.innerHTML = data.synset_addition+3986;

      var cwn2_synset = 180*118/28208;
      var cwn1_synset = 180-cwn2_synset;
      var pieData_synset = [
          {
            value: cwn2_synset,
            color: "#63AD3E"
          },
          {
            value : cwn1_synset,
            color : "#B9443C"
          }
          
      ];

      var myPie1 = new Chart(document.getElementById("lemma").getContext("2d")).Pie(pieData_lemma);
      var myPie2 = new Chart(document.getElementById("sense").getContext("2d")).Pie(pieData_sense);
      var myPie3 = new Chart(document.getElementById("facet").getContext("2d")).Pie(pieData_facet);
      var myPie4 = new Chart(document.getElementById("synset").getContext("2d")).Pie(pieData_synset);
    

      var table = document.getElementById('stats');

      table.rows[1].cells[1].innerHTML = data.sense_synonymy;
      table.rows[2].cells[1].innerHTML = data.sense_antonymy;
      table.rows[3].cells[1].innerHTML = data.sense_hypernymy;
      table.rows[4].cells[1].innerHTML = data.sense_hyponym;
      table.rows[5].cells[1].innerHTML = data.sense_holonymy;
      table.rows[6].cells[1].innerHTML = data.sense_meronymy;
      table.rows[7].cells[1].innerHTML = data.sense_near_synonymy;
      table.rows[8].cells[1].innerHTML = data.sense_paranymy;
      table.rows[9].cells[1].innerHTML = data.sense_pertainymy;
      table.rows[10].cells[1].innerHTML = data.sense_causality;
      table.rows[11].cells[1].innerHTML = data.sense_troponymy;
      table.rows[12].cells[1].innerHTML = data.sense_variant;

    });

    function stopRKey(evt) { 
      var evt = (evt) ? evt : ((event) ? event : null); 
      var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
      if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
    } 

    document.onkeypress = stopRKey; 
    
    </script>


  </body>
</html>