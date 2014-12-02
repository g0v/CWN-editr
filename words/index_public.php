<?php
$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

if (isset($_GET['usr']) && isset($_GET['id'])) {
    $_SESSION['usr'] = $_GET['usr'];
    $_SESSION['pswd'] = $_GET['id'];
}

if(!isset($_SESSION['usr']) || !isset($_SESSION['pswd'])){
 header("Location: ../signin/?ref=words");
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
    <link rel="shortcut icon" href="../img/lope.ico">

    <title>CWN editr - Words</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/dashboard.css" rel="stylesheet">

    <!-- Custom styles for Typeahead -->
    <link href="../css/typeahead.css" rel="stylesheet">

    <!-- Ladda UI -->
    <link href="../css/ladda-themeless.min.css" rel="stylesheet">

    <link href="../css/tooltipster.css" rel="stylesheet">
    <link href="../css/themes/tooltipster-shadow.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

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
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="editor"><?php echo $_SESSION['usr']; ?></span> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Editing history</a></li>
                <li class="divider"></li>
                <li><a href="../signin/logout.php">Sign out</a></li>
              </ul>
            </li>
          </ul>
          <div class="col-xs-12 col-sm-2 pull-right">
            <form class="navbar-form navbar-right">
              <div class="input-group add-on">
                <input id="searchBox" type="text" class="form-control" placeholder="Search" onkeyup="search(this.value);" autocomplete="off">
                <div class="input-group-btn">
                  <button id="searchButton" class="btn btn-default" type="button" onclick="clearSearch();"><i class="glyphicon glyphicon-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="../index_public.php"><span class="glyphicon glyphicon-stats"></span> 總覽 (Overview)</a></li>
            <li class="active"><a href="#"><span class="glyphicon glyphicon-font"></span> 單詞 (Words)</a></li>
            <!-- <li><a href="../relations/"><span class="glyphicon glyphicon-transfer"></span> 語意關係 (Relations)</a></li> -->
            <!-- <li><a href="../synsets/"><span class="glyphicon glyphicon-list"></span> 同義詞集 (Synsets)</a></li> -->
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">單詞 (Words) 
            <!-- add button-->            
            <button id="addButton" class="btn btn-default btn-xs" data-toggle="modal" data-target="#addModal"><span class="glyphicon glyphicon-plus"></span></button>
            <!-- history button-->            
            <button id="histButton" type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#histModal"><span class="glyphicon glyphicon-time"></span></button>
            <!-- export button-->
            <div class="btn-group">
              <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cloud-download"></span></button>
              <ul class="dropdown-menu " role="menu">
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'json',escape:'false'});"> <img src='../img/json.png' width='24px'> JSON</a></li>
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"> <img src='../img/json.png' width='24px'> JSON (ignoreColumn)</a></li>
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'json',escape:'true'});"> <img src='../img/json.png' width='24px'> JSON (with Escape)</a></li>
                <li class="divider"></li>
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'xml',escape:'false'});"> <img src='../img/xml.png' width='24px'> XML</a></li>
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'sql'});"> <img src='../img/sql.png' width='24px'> SQL</a></li>
                <li class="divider"></li>
                <li><a href="#" onClick ="$('#synset_table').tableExport({type:'csv',escape:'false'});"> <img src='../img/csv.png' width='24px'> CSV</a></li>
              </ul>
            </div>        
          </h1>
          <!-- addModal -->
          <div class="row">
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addModalLabel">新增單詞 (Add New Words)</h4>
                  </div>
                  <div class="modal-body">
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-12" id="suggestion" style="width:100%; overflow-x:hidden">
                      <form class="form-horizontal" role="form">
                        <div class="form-group">
                          <label for="lemmaBox" class="col-sm-3 control-label">詞形 (Lemma)</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="lemmaBox" onkeyup="lemmaSearch(this.value);">
                          </div>
                        </div>
                      </form>
                    </div>
                    <label id="msgg" style="margin: 16px; height:55px;"></label>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lg pull-right ladda-button" data-style="zoom-out" id="setButton"><span class="ladda-label">新增 (Add)</span></button>
                    <button type="button" class="btn btn-default btn-lg pull-right buffer-right" data-dismiss="modal">關閉 (Close)</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- editModal -->
          <div class="row">
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addModalLabel">編輯單詞 (Edit Words)</h4>
                  </div>
                  <div class="modal-body" style="padding:0;">
                    <center><h2 data-id="" id="topLemma">Loading...</h2></center>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="padding:9px 0 0 20px;">
                      <li class="active"><a href="#symbol" role="tab" data-toggle="tab">音標 (Phonetic Symbol)</a></li>
                      <li><a href="#supersense" role="tab" data-toggle="tab">意類 (Supersense)</a></li>
                      <li><a href="#sense" role="tab" data-toggle="tab">詞義 (Sense)</a></li>
                      <li><a href="#example" role="tab" data-toggle="tab" style="color:#CE3D3D;">刪除 (Delete)</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                      <!--symbol tab-->
                      <div class="tab-pane fade in active" id="symbol">
                        <br />
                        <br />
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                          <form class="form-horizontal" role="form">
                            <div class="form-group">
                              <label for="pinyinBox" class="col-sm-3 control-label">拼音 (Pinyin)</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" id="pinyinBox">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="zhuyinBox" class="col-sm-3 control-label">注音 (Zhuyin)</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" id="zhuyinBox">
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-sm-offset-3 col-sm-9">
                                <button type="button" class="btn btn-primary ladda-button" data-style="zoom-out" id="editSymbolButton" data-pinyin="" data-zhuyin="" onclick="editSymbol();" ><span class="ladda-label">儲存 (Save)</span></button>
                              </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-sm-1"></div>
                      </div>
                      <!--supersense tab-->
                      <div class="tab-pane fade" id="supersense">
                        <br />
                        <br />
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="text" class="form-control" id="supersenseBox">
                            <span class="input-group-btn">
                              <button class="btn btn-primary" type="button" id="editSupersenseButton">儲存 (Save)</button>
                            </span>
                          </div>
                        </div>
                        <div class="col-sm-1"></div>
                      </div>                      
                      <!--sense tab-->
                      <div class="tab-pane fade" id="sense">
                        <img style="margin:15px 0 0 30px;" src="../img/loader.gif" alt="Loading...">
                      </div>
                      <!--example tab-->
                      <div class="tab-pane fade" id="example">
                        <img style="margin:15px 0 0 30px;" src="../img/loader.gif" alt="Loading...">
                      </div>
                    </div>
                    <br />
                    <br />
                    <label id="msg" style="margin: 16px; height:77px;"></label>
                    <div class="col-sm-12" style="height:10px;"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-lg pull-right buffer-right" data-dismiss="modal">關閉 (Close)</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- histModal -->
          <div class="row">
            <div class="modal fade" id="histModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addModalLabel">詞集編輯記錄 (Synset Editing History)</h4>
                  </div>
                  <div class="modal-body">
                    <div id="history" style="width:100%; height:200px; overflow-x:hidden">
                      <img src="../img/loader.gif" alt="Loading...">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-lg pull-right buffer-right" data-dismiss="modal">關閉 (Close)</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="table">
            <img src="../img/loader.gif" alt="Loading...">
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <!-- typeahead.jquery.min.js -->
    <script src="../js/typeahead.jquery.min.js"></script>
    <!-- bloodhound.min.js -->
    <script src="../js/bloodhound.min.js"></script>

    <!-- spin.min.js -->
    <script src="../js/spin.min.js"></script>
    <!-- ladda.min.js -->
    <script src="../js/ladda.min.js"></script>
    <!-- jquery.tooltipster.min.js --> 
    <script src="../js/jquery.tooltipster.min.js"></script>

    <script>

      //lemma suggestions of synset definition
      var countries = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 10,
        remote: {
          // url points to a json file that contains an array of country names, see
          // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
          url: '../src/pwn_lemma_suggest.php?q=%QUERY',
          // the json file contains an array of strings, but the Bloodhound
          // suggestion engine expects JavaScript objects so this converts all of
          // those strings
          filter: function(list) {
            return $.map(list, function(country) { return { name: country }; });
          }
        }
      });
       
      // kicks off the loading/processing of `local` and `prefetch`
      countries.initialize();
       
      // passing in `null` for the `options` arguments will result in the default
      // options being used
      $('#typeahead_synset').typeahead(null, {
        name: 'countries',
        displayKey: 'name',
        // `ttAdapter` wraps the suggestion engine in an adapter that
        // is compatible with the typeahead jQuery plugin
        source: countries.ttAdapter()
      });

      //when addModal disappears
      $('#addModal').on('hidden.bs.modal', function () {
        $("#lemmaBox").val('');
        $("#msgg").html('');
      });

      //when editModal disappears
      var changed = 0;
      $('#editModal').on('hidden.bs.modal', function () {
        $("#sense").html('<img src="../img/loader.gif" alt="Loading...">');
        if(changed==1){
          $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
          $.get("../src/listWord.php", function(result){
            var tableHTML = $("#table").html();
            if(tableHTML=='<img src="../img/loader.gif" alt="Loading...">'){
              $("#table").html(result);
              $("#table .glyphicon-play-circle").hide();
              $("#table tr").hover(function() {
                $("#table .glyphicon-play-circle").hide();
                $(this).find(".glyphicon-play-circle").show();
              });
              $("#table").mouseleave(function() {
                $("#table .glyphicon-play-circle").hide();      
              });
              $("#table .glyphicon-play-circle").tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                contentAsHTML: true,
                interactive: true,
                interactiveTolerance: 175,
                speed: 175,
                maxWidth: 300,
                position: 'right'
              });
              //
              $("#table .glyphicon-comment").hide();
              $("#table tr").hover(function() {
                $("#table .glyphicon-comment").hide();
                $(this).find(".glyphicon-comment").show();
              });
              $("#table").mouseleave(function() {
                $("#table .glyphicon-comment").hide();      
              });
              $("#table .glyphicon-comment").tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                contentAsHTML: true,
                interactive: true,
                interactiveTolerance: 175,
                speed: 175,
                maxWidth: 300,
                position: 'right'
              }); 
            }
          });
          changed=0;
        }
      });

      //list words
      jQuery(document).ready(function(){
        $.get("../src/listWord.php", function(result){
          var tableHTML = $("#table").html().trim();
          if(tableHTML=='<img src="../img/loader.gif" alt="Loading...">'){
            $("#table").html(result);
            $("#table .glyphicon-play-circle").hide();
            $("#table tr").hover(function() {
              $("#table .glyphicon-play-circle").hide();
              $(this).find(".glyphicon-play-circle").show();
            });
            $("#table").mouseleave(function() {
              $("#table .glyphicon-play-circle").hide();      
            });
            $("#table .glyphicon-play-circle").tooltipster({
              theme: 'tooltipster-shadow',
              animation: 'fade',
              contentAsHTML: true,
              interactive: true,
              interactiveTolerance: 175,
              speed: 175,
              maxWidth: 300,
              position: 'right'
            });
            //
            $("#table .glyphicon-comment").hide();
            $("#table tr").hover(function() {
              $("#table .glyphicon-comment").hide();
              $(this).find(".glyphicon-comment").show();
            });
            $("#table").mouseleave(function() {
              $("#table .glyphicon-comment").hide();      
            });
            $("#table .glyphicon-comment").tooltipster({
              theme: 'tooltipster-shadow',
              animation: 'fade',
              contentAsHTML: true,
              interactive: true,
              interactiveTolerance: 175,
              speed: 175,
              maxWidth: 300,
              position: 'right'
            });
          };
        });
      });

      //switch pages
      function switchPages(page){
        $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
        $.get("../src/listWord.php?p=" + page, function(result){
          var tableHTML = $("#table").html();
          if(tableHTML=='<img src="../img/loader.gif" alt="Loading...">'){
            $("#table").html(result);
            $("#table .glyphicon-play-circle").hide();
            $("#table tr").hover(function() {
              $("#table .glyphicon-play-circle").hide();
              $(this).find(".glyphicon-play-circle").show();
            });
            $("#table").mouseleave(function() {
              $("#table .glyphicon-play-circle").hide();      
            });
            $("#table .glyphicon-play-circle").tooltipster({
              theme: 'tooltipster-shadow',
              animation: 'fade',
              contentAsHTML: true,
              interactive: true,
              interactiveTolerance: 175,
              speed: 175,
              maxWidth: 300,
              position: 'right'
            });
            //
            $("#table .glyphicon-comment").hide();
            $("#table tr").hover(function() {
              $("#table .glyphicon-comment").hide();
              $(this).find(".glyphicon-comment").show();
            });
            $("#table").mouseleave(function() {
              $("#table .glyphicon-comment").hide();      
            });
            $("#table .glyphicon-comment").tooltipster({
              theme: 'tooltipster-shadow',
              animation: 'fade',
              contentAsHTML: true,
              interactive: true,
              interactiveTolerance: 175,
              speed: 175,
              maxWidth: 300,
              position: 'right'
            });
          }
        })
      }

      //pass values to editModal
      $(document).on("click", ".editModal", function () {
        $("#topLemma").text($(this).data('lemma')+' ('+$(this).data('id')+')');
        $("#topLemma").attr('data-id', $(this).data('id'));
        $("#pinyinBox").val($(this).data('pinyin'));
        $("#zhuyinBox").val($(this).data('zhuyin'));
        $("#editSymbolButton").attr('data-pinyin', $(this).data('pinyin'));
        $("#editSymbolButton").attr('data-zhuyin', $(this).data('zhuyin'));
        $("#supersenseBox").val($(this).data('supersense'));
        $.get("../src/listSense.php?cwn_id=" + $(this).data('id') + "01", function(result){
          if(result){
            $("#sense").html(result);
          }
        })
      });

      //add words
      $(document).on("click", "#setButton", function () {
        var setButton = document.getElementById('setButton');
        var l = Ladda.create(setButton);
        l.start();
        var lemma = $("#lemmaBox").val();
        var editor = $('#editor').text();
        $.get("../src/addLemma.php?lemma="+lemma+"&editor="+editor, function(result){
          if(result=="Success"){
            l.stop();
            $('#addModal').modal('hide');
            $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
            $.get("../src/listWord.php", function(result){
              $("#table").html(result);
            });
          }
        })
      });
      //load history
      $(document).on("click", "#histButton", function () {
        $.get("../src/readLog.php?filter=LEMMA", function(result){
          $("#histModal .modal-body #history").html(result);
        })
      });

      function randomString(length, chars) {
          var result = '';
          for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
          return result;
      }

      //filter based on search
      var oldSearch = "";
      var bopomotones = /[\u3105-\u3129\u02CA\u02C7\u02CB\u02D9]/;
      var rString = "";
      function search(inputString){
        if ((inputString.trim()!==oldSearch)&&(inputString.trim().match(bopomotones)==null)){
          rString = randomString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
          if(inputString.length == 0) {
            $("#searchBox").removeClass("searchBox");
            $("#searchButton").html("<i class=\"glyphicon glyphicon-search\">");
            $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
            $.get("../src/listWord.php", function(result){
              var tableHTML = $("#table").html();
              if(tableHTML=='<img src="../img/loader.gif" alt="Loading...">'){
                $("#table").html(result);
                $("#table .glyphicon-play-circle").hide();
                $("#table tr").hover(function() {
                  $("#table .glyphicon-play-circle").hide();
                  $(this).find(".glyphicon-play-circle").show();
                });
                $("#table").mouseleave(function() {
                  $("#table .glyphicon-play-circle").hide();      
                });
                $("#table .glyphicon-play-circle").tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'fade',
                  contentAsHTML: true,
                  interactive: true,
                  interactiveTolerance: 175,
                  speed: 175,
                  maxWidth: 300,
                  position: 'right'
                });
                //
                $("#table .glyphicon-comment").hide();
                $("#table tr").hover(function() {
                  $("#table .glyphicon-comment").hide();
                  $(this).find(".glyphicon-comment").show();
                });
                $("#table").mouseleave(function() {
                  $("#table .glyphicon-comment").hide();      
                });
                $("#table .glyphicon-comment").tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'fade',
                  contentAsHTML: true,
                  interactive: true,
                  interactiveTolerance: 175,
                  speed: 175,
                  maxWidth: 300,
                  position: 'right'
                });
              }
            });
          }else{
            $("#searchBox").addClass("searchBox");
            $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
            $.get("../src/listWord.php?filter="+inputString+"&code="+rString, function(result){
              if (result.indexOf(rString)>-1){
                $("#table").html(result.replace(rString, ""));
                $("#table .glyphicon-play-circle").hide();
                $("#table tr").hover(function() {
                  $("#table .glyphicon-play-circle").hide();
                  $(this).find(".glyphicon-play-circle").show();
                });
                $("#table").mouseleave(function() {
                  $("#table .glyphicon-play-circle").hide();      
                });
                $("#table .glyphicon-play-circle").tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'fade',
                  contentAsHTML: true,
                  interactive: true,
                  interactiveTolerance: 175,
                  speed: 175,
                  maxWidth: 300,
                  position: 'right'
                });
                //
                $("#table .glyphicon-comment").hide();
                $("#table tr").hover(function() {
                  $("#table .glyphicon-comment").hide();
                  $(this).find(".glyphicon-comment").show();
                });
                $("#table").mouseleave(function() {
                  $("#table .glyphicon-comment").hide();      
                });
                $("#table .glyphicon-comment").tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'fade',
                  contentAsHTML: true,
                  interactive: true,
                  interactiveTolerance: 175,
                  speed: 175,
                  maxWidth: 300,
                  position: 'right'
                });
                $("#searchBox").removeClass("searchBox");
                $("#searchButton").html("<i class=\"glyphicon glyphicon-remove\">");
              }
            });
          }
          oldSearch = inputString.trim();
        }
      }
      
      //clear search
      function clearSearch(){
        oldSearch = "";
        if ($("#searchBox").val()===""){
          $("#searchBox").focus();
        }else{
          $("#searchButton").html("<i class=\"glyphicon glyphicon-search\">");
          $("#searchBox").val("");
          $("#table").html('<img src="../img/loader.gif" alt="Loading...">');
          $.get("../src/listWord.php", function(result){
            var tableHTML = $("#table").html();
            if(tableHTML=='<img src="../img/loader.gif" alt="Loading...">'){
              $("#table").html(result);
              $("#table .glyphicon-play-circle").hide();
              $("#table tr").hover(function() {
                $("#table .glyphicon-play-circle").hide();
                $(this).find(".glyphicon-play-circle").show();
              });
              $("#table").mouseleave(function() {
                $("#table .glyphicon-play-circle").hide();      
              });
              $("#table .glyphicon-play-circle").tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                contentAsHTML: true,
                interactive: true,
                interactiveTolerance: 175,
                speed: 175,
                maxWidth: 300,
                position: 'right'
              });
              //
              $("#table .glyphicon-comment").hide();
              $("#table tr").hover(function() {
                $("#table .glyphicon-comment").hide();
                $(this).find(".glyphicon-comment").show();
              });
              $("#table").mouseleave(function() {
                $("#table .glyphicon-comment").hide();      
              });
              $("#table .glyphicon-comment").tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                contentAsHTML: true,
                interactive: true,
                interactiveTolerance: 175,
                speed: 175,
                maxWidth: 300,
                position: 'right'
              }); 
            }
          });
          $("#searchBox").focus();
        }
      }

      //prevent enter key from submitting
      function stopRKey(evt) { 
        var evt = (evt) ? evt : ((event) ? event : null); 
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
        if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
      } 

      document.onkeypress = stopRKey;

      var oldLemma = "";
      var bopomotones = /[\u3105-\u3129\u02CA\u02C7\u02CB\u02D9]/;
      function lemmaSearch(inputString){
        if ((inputString.trim()!==oldLemma)&&(inputString.trim().match(bopomotones)==null)){
          if(inputString.length == 0) {
            setTimeout(function(){ $('.alert-info').fadeOut() }, 100);
          }else{
            $.get("../src/lemmaSearch.php?q="+inputString, function(result){
              if(result){
                $("#msgg").html('<div class="alert alert-info">'+result+'</div>');
              }else{
                setTimeout(function(){ $('.alert-info').fadeOut() }, 100);
              }
            });
          }
          oldLemma = inputString.trim();
        }
      }

      //senes selcet
      function changeSense(inputString){
        if(inputString=="addSense"){
          $("#facetLabel").hide();
          $("#facetSelect").hide();        
          $("#posSelect").val("nogood"); 
          $("#glossBox").val('');
          $("#exampleBox").val('');
          $("#noteBox").val('');
        }else{
          $.get("../src/listSense.php?cwn_id=" + inputString, function(result){
            if(result){
              $("#sense").html(result);
            }
          })
        }  
      };

      //facet selcet
      function changeFacet(inputString){
        if(inputString=="addFacet"){
          $("#posSelect").val("nogood"); 
          $("#glossBox").val('');
          $("#exampleBox").val('');
          $("#noteBox").val('');
        }else{
          $.get("../src/listSense.php?cwn_id=" + inputString, function(result){
            if(result){
              $("#sense").html(result);
            }
          })
        }
      };

      function pad(num, size) {
          var s = "000000000" + num;
          return s.substr(s.length-size);
      }

      //save lemma edition
      function editLemma(){
        var editLemmaButton = document.getElementById('editLemmaButton');
        var l = Ladda.create(editLemmaButton);
        l.start();
        var pos = $("#posSelect").val();
        var gloss = $("#glossBox").val();
        var example = $("#exampleBox").val();
        var note = $("#noteBox").val();
        var editor = $('#editor').text();
        if($("#senseSelect").val()==$("#facetSelect").val()||$("#senseSelect").val()=="addSense"){
          //save a sense
          var cwn_id = $("#senseSelect").val();
          if(cwn_id=="addSense"){
            if($("#senseSelect option").length==1){
              cwn_id = $("#topLemma").attr('data-id')+"01";
            }else{
              cwn_id_largest = $("#senseSelect option:last").prev().val();
              cwn_id = pad(parseInt(cwn_id_largest)+1,8);
            }
          }
        }else{
          //save a facet
          var cwn_id= $("#facetSelect").val();
          if(cwn_id=="addFacet"){
            cwn_id_largest = $("#facetSelect option:last").prev().val();
            if(cwn_id_largest.length==8){
              cwn_id = cwn_id_largest+"01"; 
            }else{
              cwn_id = pad(parseInt(cwn_id_largest)+1,10);
            }
          }
        }
        $.get("../src/editLemma.php?cwn_id="+cwn_id+"&pos="+pos+"&gloss="+gloss+"&example="+example+"&note="+note+"&editor="+editor, function(result){
          if(result=="Success"){
            l.stop();
            $.get("../src/listSense.php?cwn_id=" + cwn_id, function(result){
              if(result){
                $("#sense").html(result);
              }
            })
            changed = 1;
          }
        })
      };

      //save phonetic symbol edition
      function editSymbol(){
        var editSymbolButton = document.getElementById('editSymbolButton');
        var l = Ladda.create(editSymbolButton);
        l.start();
        var lemma_id = $("#topLemma").attr('data-id');
        var old_pinyin = $("#editSymbolButton").data("pinyin");
        var old_zhuyin = $("#editSymbolButton").data("zhuyin");
        var new_pinyin = $("#pinyinBox").val();
        var new_zhuyin = $("#zhuyinBox").val();
        var editor = $('#editor').text();
        if(old_pinyin==new_pinyin&&old_zhuyin==new_zhuyin){
          //alert("same");
        }
        $.get("../src/editSymbol.php?lemma_id="+lemma_id+"&zhuyin="+new_zhuyin+"&pinyin="+new_pinyin+"&editor="+editor, function(result){
          if(result=="Success"){
            l.stop();
            changed = 1;
          }
        })
      };
    </script>
    
    <!-- htmltable_export -->
    <script type="text/javascript" src="../js/tableExport.js"></script>
    <script type="text/javascript" src="../js/jquery.base64.js"></script>
    <script type="text/javascript" src="../js/html2canvas.js"></script>
    <script type="text/javascript" src="../js/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="../js/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="../js/jspdf/libs/base64.js"></script>

  </body>
</html>