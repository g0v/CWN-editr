<?php
$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();
if(!isset($_SESSION['usr']) || !isset($_SESSION['pswd'])){
 header("Location: ../signin/?ref=synsets");
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

    <title>CWN editr - Synsets</title>

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
            <li><a href="../"><span class="glyphicon glyphicon-stats"></span> 總覽 (Overview)</a></li>
            <li><a href="../words/"><span class="glyphicon glyphicon-font"></span> 單詞 (Words)</a></li>
            <li><a href="../relations/"><span class="glyphicon glyphicon-transfer"></span> 語意關係 (Relations)</a></li>
            <li class="active"><a href="#"><span class="glyphicon glyphicon-list"></span> 同義詞集 (Synsets)</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">同義詞集 (Synsets) 
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
                    <h4 class="modal-title" id="addModalLabel">新增詞集成員 (Add Synset Members)</h4>
                  </div>
                  <div class="modal-body">
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-12" id="suggestion" style="width:100%; overflow-x:hidden">
                      <img src="../img/loader.gif" alt="Loading..." />
                    </div>
                    <label id="msgg" style="margin: 16px; height:15px;"></label>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lg pull-right ladda-button" data-style="zoom-out" id="setButton"><span class="ladda-label">新增 (Add)</span></button>
                    <button type="button" class="btn btn-success btn-lg pull-right ladda-button buffer-right" data-style="zoom-out" id="skipButton"><span class="ladda-label">略過 (Skip)</span></button>
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
                    <h4 class="modal-title" id="addModalLabel">編輯 PWN 對應 (Edit PWN Equivalents)</h4>
                  </div>
                  <div class="modal-body">
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-5">
                      <input class="form-control input-lg" id="typeahead_synset" type="text" placeholder="英文單詞 (English Word)">
                    </div>
                    <div class="col-sm-7">
                      <div id="suggestions"> 
                        <div class="suggestionList" id="suggestionsList">
                          <select id="synset_list" class="form-control input-lg">
                            <option value="nogood">先輸入英文單詞 (enter English Word first)</option>
                          </select>
                        </div>
                      </div>
                    </div>                    
                    <label id="msg" style="margin: 16px; height:15px;"></label>
                    <div class="col-sm-12" style="height:10px;"></div>
                  </div>
                  <div class="modal-footer">
                    <button id="editButton" type="button" class="btn btn-primary btn-lg pull-right ladda-button" data-id="" data-pwn_id="" data-pwn_gloss="" data-pwn_word="" data-editor="" data-style="zoom-out"><span class="ladda-label">儲存 (Save)</span></button>
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
                      <img src="../img/loader.gif" alt="Loading..." />
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
            <img src="../img/loader.gif" alt="Loading..." />
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

      //load synset member suggestion
      $(document).on("click", "#addButton", function () {
        $.get("../src/member_suggest.php", function(result){
          if(result){
            $("#addModal .modal-body #suggestion").html(result);
            $('#plz').tooltipster({
              theme: 'tooltipster-shadow',
              animation: 'grow',
              contentAsHTML: true,
              interactive: true,
              maxWidth: 250,
            });
          }
        })
      });

      //set synset_id as 'skippped'
      $(document).on("click", "#skipButton", function () {
        var skipButton = document.getElementById('skipButton');
        var l = Ladda.create(skipButton);
        l.start();
        var sense_id = $("#senseHeading").attr('data-id');
        var synset_id = $("#synsetHeading").attr('data-id');
        var editor = $('#editor').text();
        $.get("../src/setSynset_id.php?skip=yes&sense_id=" + sense_id + "&synset_id=" + synset_id + "&editor=" + editor, function(result){
          if(result=="Success"){
            $.get("../src/member_suggest.php", function(result){
              if(result){
                l.stop();               
                $("#addModal .modal-body #suggestion").html(result);
                $('#plz').tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'grow',
                  contentAsHTML: true,
                  interactive: true,
                  maxWidth: 250,
                });
              }
            })
          };
        })
      });

      //set synset_id as that of suggested synsest
      $(document).on("click", "#setButton", function () {
        if($('#sense_cb').prop('checked')==false&&$('#synset_cb').prop('checked')==false){
          $("#msgg").html('<div class="alert alert-danger">選擇一個釋義！(Choose one gloss!)</div>');
          setTimeout(function(){ $('.alert-danger').fadeOut() }, 2500);
          return;
        }

        if(($('#sense_cb').prop('checked')==true&&$('#sense_ta').val().length==0)||($('#synset_cb').prop('checked')==true&&$('#synset_ta').val().length==0)){
          $("#msgg").html('<div class="alert alert-danger">釋義不得空白 (The gloss cannot be empty)</div>');
          setTimeout(function(){ $('.alert-danger').fadeOut() }, 2500);           
          return;
        }
        var setButton = document.getElementById('setButton');
        var l = Ladda.create(setButton);
        l.start();
        var sense_id = $("#senseHeading").attr('data-id');        
        var synset_id = $("#synsetHeading").attr('data-id');
        var editor = $('#editor').text();
        //var sense_gloss = $("#senseBody").attr('data-gloss');        
        //var synset_gloss = $("#synsetBody").attr('data-gloss');  
        var skip="";
        if($('#sense_cb').prop('checked')==true){
           skip = $("#sense_ta").val();
        } 
        if($('#synset_cb').prop('checked')==true){
           skip = $("#synset_ta").val();
        }      
        //alert("skip:" + skip);
        $.get("../src/setSynset_id.php?skip=" + skip + "&sense_id=" + sense_id + "&synset_id=" + synset_id + "&editor=" + editor, function(result){
          if(result=="Success"){
            $.get("../src/member_suggest.php", function(result){
              if(result){
                l.stop();               
                $("#addModal .modal-body #suggestion").html(result);
                $('#plz').tooltipster({
                  theme: 'tooltipster-shadow',
                  animation: 'grow',
                  contentAsHTML: true,
                  interactive: true,
                  maxWidth: 250,
                });
                $("#msgg").html('<div class="alert alert-success">新增成功！(Added!)</div>');
                setTimeout(function(){ $('.alert-success').fadeOut() }, 2500);
                $.get("../src/listSynset.php", function(result){
                  $("#table").html(result);
                  $("#searchBox").val("");                    
                });
              }
            })
          };
        })
      });

      //when addModal disappears
      $('#addModal').on('hidden.bs.modal', function () {
        $("#addModal .modal-body #suggestion").html('<img src="../img/loader.gif" alt="Loading..."/>');
      });

      //suggestions of synset
      oldInput = "";
      function suggest_synset(inputString){
        if(inputString!==oldInput){
          var inputValue = document.getElementById('suggestionsList');
          if(inputString.length == 0) {
            inputValue.innerHTML='<select id="synset_list" class="form-control input-lg"><option value="nogood">先輸入來源詞 (enter Source Word first)</option></select>';
            }else{
              $.post("../src/pwn_synset_suggest.php", {queryString: ""+inputString+""}, function(data){
              if(data.length >0) {
                $('#suggestionsList').html(data);
              }
            });
          }
          oldInput = inputString;
        }
      }

      //list synsets
      jQuery(document).ready(function(){
        $.get("../src/listSynset.php", function(result){
          $("#table").html(result);
        });
      });

      //switch pages
      function switchPages(page){
        $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
        $.get("../src/listSynset.php?p=" + page, function(result){
          $("#table").html(result);
        })
      }

      //pass values to editModal
      var synset_handle = "";
      var synset_list_handle = "";
      $(document).on("click", ".editModal", function () {
        synset_handle = setInterval(function() { suggest_synset($('#typeahead_synset').val()); }, 100);
        $("#editButton").attr('data-id', $(this).data('id'));
        $("#editButton").attr('data-pwn_id', $(this).data('pwn_id'));
        $("#editButton").attr('data-pwn_gloss', $(this).data('pwn_gloss'));
        $("#editButton").attr('data-pwn_word', $(this).data('pwn_word'));
        //restore to default values
        $("#typeahead_synset").val("");
        $("#synset_list option").remove();
        $("#synset_list").append($("<option></option>").attr("value", 'nogood').text("先輸入英文單詞 (enter English Word first)"));
        $("#typeahead_synset").val($(this).data('pwn_word'));
        if($(this).data('pwn_gloss')!==""){
          $("#synset_list").append($("<option></option>").attr("value", $(this).data('id')).text($(this).data('pwn_gloss')));
          synset_list_handle = setInterval(function() { $("#synset_list").val($('#editButton').attr('data-pwn_id'));}, 100);
        }
        //suggest_synset($('#typeahead_synset').val());
        //read history if it exists
        //if($(this).data('pwn_word')!==""&&$(this).data('pwn_id')!==""&&$(this).data('pwn_gloss').length!==""){
          //$("#synset_list").val($(this).data('pwn_id'));
        //}
      });

      $(document).on("click", "#synset_list", function(){
        clearInterval(synset_list_handle);
      });

      $(document).on("click", "#typeahead_synset", function(){
        clearInterval(synset_list_handle);
      });

      //pass values to editSynset.php
      $(document).on("click", "#editButton",function () {
        if(($("#typeahead_synset").val()!=="")&&($("#synset_list").val()!=="nogood")){
          var addButton = document.getElementById('editButton');
          var l = Ladda.create(addButton);
          l.start();
          var id = $(this).attr('data-id');
          var pwn_id = $("#synset_list").val();
          var pwn_id_gloss = $("#synset_list option:selected").text().split(/([\d]{9})./);
          var pwn_gloss = pwn_id_gloss[2];
          var pwn_word = $("#typeahead_synset").val();
          var editor = $('#editor').text();
          var suffix = "?id=" + id + "&pwn_id=" + pwn_id + "&pwn_gloss=" + pwn_gloss + "&pwn_word=" + pwn_word + "&editor=" + editor;
          var current_page = $("ul.pagination li.active a").text();
          $.get("../src/editSynset.php" + encodeURI(suffix), function(result){
            if(result=="Success"){
              filter = $("#searchBox").val();
              if(filter===""){
                $.get("../src/listSynset.php?p="+current_page, function(result){
                  $("#table").html(result);
                })
              }else{
                $.get("../src/listSynset.php?code=123&filter="+filter, function(result){
                  $("#table").html(result.replace("123", ""));
                })    
              }
              l.stop();
              $('#editModal').modal('hide');
              clearInterval(synset_handle);
              clearInterval(synset_list_handle);
            };
          })
        }else{
          $("#msg").html('<div class="alert alert-danger">檢查一下欄位吧！(Check the fields!)</div>');
          setTimeout(function(){ $('.alert-danger').fadeOut() }, 2500);
        }
      });

      //when editModal disappears
      $('#editModal').on('hidden.bs.modal', function () {
        $('#msg').html('');
        $('#typeahead_synset').val('');
        oldInput = "";
        clearInterval(synset_handle);
        clearInterval(synset_list_handle);
      });

      //load history
      $(document).on("click", "#histButton", function () {
        $.get("../src/readLog.php?filter=to", function(result){
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
            $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
            $.get("../src/listSynset.php", function(result){
              $("#table").html(result);
            });
          }else{
            $("#searchBox").addClass("searchBox");
            $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
            $.get("../src/listSynset.php?filter="+inputString+"&code="+rString, function(result){
              if (result.indexOf(rString)>-1){
                $("#table").html(result.replace(rString, ""));
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
          $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
          $.get("../src/listSynset.php", function(result){
            $("#table").html(result);
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

      if ($('#editor').text()!="LOPE"){
        $('#addButton').prop('disabled', true);
      }

      //chechbox controls
      $(document).on("change", "#sense_cb",function () {
        if($('#sense_cb').is(":checked")){
          $('#sense_ta').prop('disabled', false);
          $('#sense_ta').focus();
          $('#synset_cb').prop('checked', false);
          $('#synset_ta').prop('disabled', true);
          $('#synset_ta').val($("#synsetBody").attr('data-gloss'));
        }else{
          $('#sense_ta').prop('disabled', true);
          $('#sense_ta').val($("#senseBody").attr('data-gloss'));
        }
      });
      $(document).on("change", "#synset_cb",function () {
        if($('#synset_cb').is(":checked")){
          $('#synset_ta').prop('disabled', false);
          $('#synset_ta').focus();
          $('#sense_cb').prop('checked', false);
          $('#sense_ta').prop('disabled', true);
          $('#sense_ta').val($("#senseBody").attr('data-gloss'));
        }else{
          $('#synset_ta').prop('disabled', true);
          $('#synset_ta').val($("#synsetBody").attr('data-gloss'));
        }
      });

      //go to page
      function goToPage(){
        page = $('#pageNumber').val();
        $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
        $.get("../src/listSynset.php?p=" + page, function(result){
          $("#table").html(result);
        }) 
      }
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