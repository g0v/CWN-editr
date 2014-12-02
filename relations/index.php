<?php
$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();
if(!isset($_SESSION['usr']) || !isset($_SESSION['pswd'])){
 header("Location: ../signin/?ref=relations");
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

    <title>CWN editr - Relations</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/dashboard.css" rel="stylesheet">

    <!-- Custom styles for Typeahead -->
    <link href="../css/typeahead.css" rel="stylesheet">

    <!-- Ladda UI -->
    <link href="../css/ladda-themeless.min.css" rel="stylesheet">

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
            <li class="active"><a href="#"><span class="glyphicon glyphicon-transfer"></span> 語意關係 (Relations)</a></li>
            <li><a href="../synsets/"><span class="glyphicon glyphicon-list"></span> 同義詞集 (Synsets)</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">語意關係 (Relations) 
            <!-- add button-->            
            <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#addModal"><span class="glyphicon glyphicon-plus"></span></button>
            <!-- history button-->            
            <button id="histButton" type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#histModal"><span class="glyphicon glyphicon-time"></span></button>
            <!-- export button-->
            <div class="btn-group">
              <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cloud-download"></span></button>
              <ul class="dropdown-menu " role="menu">
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'json',escape:'false'});"> <img src='../img/json.png' width='24px'> JSON</a></li>
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"> <img src='../img/json.png' width='24px'> JSON (ignoreColumn)</a></li>
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'json',escape:'true'});"> <img src='../img/json.png' width='24px'> JSON (with Escape)</a></li>
                <li class="divider"></li>
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'xml',escape:'false'});"> <img src='../img/xml.png' width='24px'> XML</a></li>
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'sql'});"> <img src='../img/sql.png' width='24px'> SQL</a></li>
                <li class="divider"></li>
                <li><a href="#" onClick ="$('#relation_table').tableExport({type:'csv',escape:'false'});"> <img src='../img/csv.png' width='24px'> CSV</a></li>
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
                    <h4 class="modal-title" id="addModalLabel">新增語意關係 (Add Semantic Relations)</h4>
                  </div>
                  <div class="modal-body">
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-5">
                      <input class="form-control input-lg" id="typeahead_source" type="text" placeholder="來源詞 (Source Word)" onkeyup="suggest(this.value);">
                    </div>
                    <div class="col-sm-7">
                      <div id="suggestions"> 
                        <div class="suggestionList" id="suggestionsList">
                          <select id="source_list" class="form-control input-lg">
                            <option value="nogood">先輸入來源詞 (enter Source Word first)</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-5">
                      <input class="form-control input-lg" id="typeahead_target" type="text" placeholder="標的詞 (Target Word)" onkeyup="suggest2(this.value);">
                    </div>
                    <div class="col-sm-7">
                      <div id="suggestions"> 
                        <div class="suggestionList2" id="suggestionsList2">
                          <select id="target_list" class="form-control input-lg">
                            <option value="nogood">先輸入標的詞 (enter Target Word first)</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12" style="height:10px;"></div>
                    <div class="col-sm-12">
                      <select id="relations_list" class="form-control input-lg">
                        <option value="=">同義關係 (synonymy)</option>
                        <option value="!">反義關係 (antonymy)</option>
                        <option value="@">上位關係 (hypernymy)</option>
                        <option value="~">下位關係 (hyponym)</option>
                        <option value="#">整體關係 (holonymy)</option>
                        <option value="|%">部分關係 (meronymy)</option>
                        <option value="&">近義關係 (near-synonymy)</option>
                        <option value="+">類義關係 (paranymy)</option>
                        <option value="?">附屬關係 (pertainymy)</option>
                        <option value=">">致使關係 (causality)</option>
                        <option value="<">方式關係 (troponymy)</option>
                        <option value="^">異體關係 (variant)</option>
                      </select>
                    </div>
                    <div class="col-sm-12">
                      <div class="checkbox pull-right">
                        <label id="direction">
                          <input type="checkbox" id="bidirection"> 雙向關係 (two-way relationship)
                        </label>
                      </div>
                    </div>
                    <label id="msg" style="margin: 16px; height:15px;"></label>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lg pull-right ladda-button" data-style="zoom-out" onclick="addRelation()" id="addButton"><span class="ladda-label">新增 (Add)</span></button>
                    <button type="button" class="btn btn-default btn-lg pull-right buffer-right" data-dismiss="modal">關閉 (Close)</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- delModal -->
          <div class="row">
            <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addModalLabel">刪除語意關係 (Delete Semantic Relations)</h4>
                  </div>
                  <div class="modal-body">
                    <center><h3 style="color: #CE3D3D;">確定要刪除嗎？(Confirm this deletion)</h3></center>
                  </div>
                  <div class="modal-footer">
                    <button id="delButton" type="button" class="btn btn-danger btn-lg pull-right" data-cwn_id="" data-rel_type="" data-rel_cwnid="" data-rel_facet="" data-editor="">刪除 (Delete)</button>
                    <button type="button" class="btn btn-default btn-lg pull-right buffer-right" data-dismiss="modal">取消 (Cancel)</button>
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
                    <h4 class="modal-title" id="addModalLabel">關係編輯記錄 (Relation Editing History)</h4>
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
 
    <script>
      //lemma suggestions of source word
      var countries = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 10,
        remote: {
          // url points to a json file that contains an array of country names, see
          // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
          url: '../src/lemma_suggest.php?q=%QUERY',
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
      $('#typeahead_source').typeahead(null, {
        name: 'countries',
        displayKey: 'name',
        // `ttAdapter` wraps the suggestion engine in an adapter that
        // is compatible with the typeahead jQuery plugin
        source: countries.ttAdapter()
      });

      //lemma suggestions of target word
      var countries2 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 10,
        remote: {
          // url points to a json file that contains an array of country names, see
          // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
          url: '../src/lemma_suggest2.php?q=%QUERY',
          // the json file contains an array of strings, but the Bloodhound
          // suggestion engine expects JavaScript objects so this converts all of
          // those strings
          filter: function(list) {
            return $.map(list, function(country) { return { name: country }; });
          }
        }
      });
       
      // kicks off the loading/processing of `local` and `prefetch`
      countries2.initialize();
       
      // passing in `null` for the `options` arguments will result in the default
      // options being used
      $('#typeahead_target').typeahead(null, {
        name: 'countries',
        displayKey: 'name',
        // `ttAdapter` wraps the suggestion engine in an adapter that
        // is compatible with the typeahead jQuery plugin
        source: countries2.ttAdapter()
      });

      //sense suggestions of source word
      var oldSource = "";
      var bopomotones = /[\u3105-\u3129\u02CA\u02C7\u02CB\u02D9]/;
      function suggest(inputString){
        if(inputString!==oldSource&&(inputString.trim().match(bopomotones)==null)){
          if(inputString.length == 0) {
            $('#source_list').find('option').remove().end().append('<option value="nogood">先輸入來源詞 (enter Source Word first)</option>').val('nogood');
          }else{
            $.post("../src/sense_suggest.php", {queryString: ""+inputString+""}, function(data){
              if(data.length >0) {
                $('#suggestions').fadeIn();
                $('#suggestionsList').html(data);
              }
            });
          }
          oldSource = inputString;
        }
      }

      setInterval(function() { suggest($('#typeahead_source').val()); }, 100);

      //sense suggestions of target word
      var oldTarget = "";
      var bopomotones = /[\u3105-\u3129\u02CA\u02C7\u02CB\u02D9]/;
      function suggest2(inputString){
        if(inputString!==oldTarget){
          var inputValue = document.getElementById('suggestionsList2');
          if(inputString.length == 0) {
            $('#target_list').find('option').remove().end().append('<option value="nogood">先輸入標的詞 (enter Target Word first)</option>').val('nogood');
          }else{
            $.post("../src/sense_suggest2.php", {queryString: ""+inputString+""}, function(data){
              if(data.length >0) {
                $('#suggestions2').fadeIn();
                $('#suggestionsList2').html(data);
              }
            });
          }
          oldTarget = inputString;
        }
      }

      setInterval(function() { suggest2($('#typeahead_target').val()); }, 100);

      //when addModal disappears
      $('#addModal').on('hidden.bs.modal', function () {
        $('#msg').html('');
        $('#typeahead_source').val('');
        $('#typeahead_target').val('');
        $('#source_list').find('option').remove().end().append('<option value="nogood">先輸入來源詞 (enter Source Word first)</option>').val('nogood');
        $('#target_list').find('option').remove().end().append('<option value="nogood">先輸入標的詞 (enter Target Word first)</option>').val('nogood');
        $('#relations_list').val('=');
        $('#bidirection').attr('checked', false);
        $('#direction').show();
        oldSource = "";
        oldTarget = "";
      });

      //list relations
      jQuery(document).ready(function(){
        $.get("../src/listRelation.php", function(result){
          $("#table").html(result);
        });
      });

      //switch pages
      function switchPages(page){
        $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
        $.get("../src/listRelation.php?p=" + page, function(result){
          $("#table").html(result);
        })
      }

      //pass values to delModal
      $(document).on("click", ".delModal", function () {
        $("#delButton").attr('data-cwn_id', $(this).data('cwn_id'));
        $("#delButton").attr('data-rel_type', $(this).data('rel_type'));
        $("#delButton").attr('data-rel_cwnid', $(this).data('rel_cwnid'));
        $("#delButton").attr('data-rel_facet', $(this).data('rel_facet'));
        //$("#delModal .modal-body").html("<center><h3 style=\"color: #CE3D3D;\">確定要刪除嗎？(Confirm this deletion)</h3></center>");
      });

      //pass values to delRelation.php
      $(document).on("click", "#delButton",function () {
        var cwn_id = $(this).attr('data-cwn_id');
        var rel_type = encodeURIComponent($(this).attr('data-rel_type'));
        var rel_cwnid = $(this).attr('data-rel_cwnid');
        var rel_facet = $(this).attr('data-rel_facet');
        var editor = $('#editor').text();
        var suffix = "?cwn_id=" + cwn_id + "&rel_type=" + rel_type + "&rel_cwnid=" + rel_cwnid + "&rel_facet=" + rel_facet + "&editor=" + editor;
        var current_page = $("ul.pagination li.active a").text();
        $.get("../src/delRelation.php" + encodeURI(suffix), function(result){
          if(result=="Success"){
            filter = $("#searchBox").val();
            if(filter===""){
              $.get("../src/listRelation.php?p="+current_page, function(result){
                $("#table").html(result);
              })
            }else{
              $.get("../src/listRelation.php?code=123&filter="+filter, function(result){
                $("#table").html(result.replace("123", ""));
              })    
            }
            $('#delModal').modal('hide');
          };
        })

      });

      //show/hide bidirection
      $("#relations_list").change(function() {
        $("#direction").show();
        var noshow = ["?",">","<"];
        if($.inArray($("#relations_list").find(":selected").val(), noshow)!=-1){
          $("#bidirection").attr('checked', false);                 
          $("#direction").hide();
        }
      });

      //load history
      $(document).on("click", "#histButton", function () {
        $.get("../src/readLog.php?filter=and", function(result){
          $("#histModal .modal-body #history").html(result);
        })
      });

      function randomString(length, chars) {
          var result = '';
          for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
          return result;
      }

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
            $.get("../src/listRelation.php", function(result){
              $("#table").html(result);
            });
          }else{
            $("#searchBox").addClass("searchBox");
            $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
            $.get("../src/listRelation.php?filter="+inputString+"&code="+rString, function(result){
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
      
      function clearSearch(){
        oldSearch = "";
        if ($("#searchBox").val()===""){
          $("#searchBox").focus();
        }else{
          $("#searchButton").html("<i class=\"glyphicon glyphicon-search\">");
          $("#searchBox").val("");
          $("#table").html('<img src="../img/loader.gif" alt="Loading..."/>');
          $.get("../src/listRelation.php", function(result){
            $("#table").html(result);
          });
          $("#searchBox").focus();
        }
      }

    function stopRKey(evt) { 
      var evt = (evt) ? evt : ((event) ? event : null); 
      var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
      if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
    } 

    document.onkeypress = stopRKey;

    </script>

    <!-- addRelation.js -->
    <script src="../js/addRelation.js"></script>
    
    <!-- htmltable_export -->
    <script type="text/javascript" src="../js/tableExport.js"></script>
    <script type="text/javascript" src="../js/jquery.base64.js"></script>
    <script type="text/javascript" src="../js/html2canvas.js"></script>
    <script type="text/javascript" src="../js/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="../js/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="../js/jspdf/libs/base64.js"></script>

  </body>
</html>