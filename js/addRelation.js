function addRelation(){

	var source_list = document.getElementById('source_list');
	var target_list = document.getElementById('target_list');
	var msg = document.getElementById('msg');

	if (source_list.value=="nogood"||target_list.value=="nogood"){
		msg.innerHTML = '<div class="alert alert-danger">檢查一下欄位吧！(Check the fields!)</div>';
		setTimeout(function(){ $('.alert-danger').fadeOut() }, 2500);
	}else{
		var relations_list = document.getElementById('relations_list').value;
		source_def = source_list.options[source_list.selectedIndex].text.split(" ");
		target_def = target_list.options[target_list.selectedIndex].text.split(" ");
		if (relations_list=="="&&source_def[1]!=target_def[1]){
			msg.innerHTML = '<div class="alert alert-info">同義詞必須有相同的釋義 (Synonyms should have an identical gloss)</div>';
			setTimeout(function(){ $('.alert-info').fadeOut() }, 2500);
		}else{
			var addButton = document.getElementById('addButton');
			var l = Ladda.create(addButton);
		 	l.start();

			var ajaxRequest;  // The variable that makes Ajax possible!
			
			try{
				// Opera 8.0+, Firefox, Safari
				ajaxRequest = new XMLHttpRequest();
			} catch (e){
				// Internet Explorer Browsers
				try{
					ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try{
						ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
					} catch (e){
						// Something went wrong
						alert("Your browser broke!");
						return false;
					}
				}
			}
			// Create a function that will receive data sent from the server
			ajaxRequest.onreadystatechange = function(){
				if(ajaxRequest.readyState == 4){
					if(ajaxRequest.responseText.length == 1){
						if(ajaxRequest.responseText=="y"){
							l.stop();
							msg.innerHTML = '<div class="alert alert-success">新增成功！(Added!)</div>';
							setTimeout(function(){ $('.alert-success').fadeOut() }, 2500);
							$.get("../src/listRelation.php", function(result){
					          $("#table").html(result);
					        });
					        $("#searchBox").val("");					          
						}else{
							l.stop();
							msg.innerHTML = '<div class="alert alert-warning">此關係已存在 (The relation already exists)</div>';
							setTimeout(function(){ $('.alert-warning').fadeOut() }, 2500);
						}
					}else if(ajaxRequest.responseText.length == 2){
						if(ajaxRequest.responseText=="yy"){
							l.stop();
							msg.innerHTML = '<div class="alert alert-success">新增成功！(Added!)</div>';
							setTimeout(function(){ $('.alert-success').fadeOut() }, 2500);
							$.get("../src/listRelation.php", function(result){
					          $("#table").html(result);
					        });
					        $("#searchBox").val("");					          
						}else if(ajaxRequest.responseText=="yn"){
							l.stop();
							msg.innerHTML = '<div class="alert alert-success">新增成功 (Added!)</div><div class="alert alert-warning">逆向關係已存在 (The reverse relation already exists)</div>';
							setTimeout(function(){ $('.alert-success').fadeOut() }, 2500);
							setTimeout(function(){ $('.alert-warning').fadeOut() }, 2500);
							$.get("../src/listRelation.php", function(result){
					          $("#table").html(result);
					        });
					        $("#searchBox").val("");					          
						}else if(ajaxRequest.responseText=="ny"){
							l.stop();
							msg.innerHTML = '<div class="alert alert-warning">此關係已存在 (The relation already exists)</div><div class="alert alert-success">逆向關係新增成功 (The reverse relation added)</div>';
							setTimeout(function(){ $('.alert-warning').fadeOut() }, 2500);
							setTimeout(function(){ $('.alert-success').fadeOut() }, 2500);
							$.get("../src/listRelation.php", function(result){
					          $("#table").html(result);
					        });						
						}else{
							l.stop();
							msg.innerHTML = '<div class="alert alert-warning">順逆向關係皆已存在 (The two-way relation already exists)</div>';
							setTimeout(function(){ $('.alert-warning').fadeOut() }, 2500);
						}	
					}else{
							l.stop();
							msg.innerHTML = '<div class="alert alert-danger">資料庫忙碌中，請再試一次 (The database is busy. Try again)</div>';
							setTimeout(function(){ $('.alert-danger').fadeOut() }, 2500);						
					}
				}
			}
			var relation = document.getElementById('relations_list').value;
			relation = encodeURIComponent(relation);
			var lemmaplusid = document.getElementById('typeahead_target').value;
			var lemma = lemmaplusid.split(" ");
			var lemmaplusid2 = document.getElementById('typeahead_source').value;
			var lemma2 = lemmaplusid2.split(" ");
			var editor = document.getElementById('editor').innerHTML;

			var queryString = "?source=" + source_list.value + "&relation=" + relation + "&lemma=" + lemma[0] + "&target=" + target_list.value + "&editor=" + editor + "&lemma2=" + lemma2[0];

			if(document.getElementById('bidirection').checked){
				queryString += "&bidirection=yes"; 
			}
			ajaxRequest.open("GET", "../src/addRelation.php" + encodeURI(queryString), true);
			//alert(queryString);
			ajaxRequest.send(null);	
		}
	}
}