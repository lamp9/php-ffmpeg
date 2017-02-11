	//全选反选
	var yes_no = 0;
	function checkAll() {
		//var code_Values = document.getElementsByTagName("input");
		var code_Values = document.getElementsByName("batch[]");
		if(yes_no == 0){
			for(i = 0;i < code_Values.length;i++){
				if(code_Values[i].type == "checkbox") {
					code_Values[i].checked = true; 
				}
			}
			yes_no = 1;
		} else {
			for(i = 0;i < code_Values.length;i++){ 
				if(code_Values[i].type == "checkbox") { 
					code_Values[i].checked = false; 
				} 
			}
			yes_no = 0;
		}
	}
	
	function checkAll_true() {
		var code_Values = document.getElementsByName("id[]");
		for(i = 0;i < code_Values.length;i++){
			if(code_Values[i].type == "checkbox") {
				code_Values[i].checked = true; 
			}
		}
	}
	
	//反选
	function uncheckAll() { 
	  var code_Values = document.getElementsByTagName("input"); 
	  for(i = 0;i < code_Values.length;i++){ 
		if(code_Values[i].type == "checkbox") { 
			code_Values[i].checked = false; 
		} 
	  } 
	}
	//反选（逻辑）
	function un_check() {
	  var code_Values = document.getElementsByTagName("input"); 
	  for(i = 0;i < code_Values.length;i++){ 
		if(code_Values[i].type == "checkbox") {
			if(code_Values[i].checked) {
				code_Values[i].checked = false;
			} else {
				code_Values[i].checked = true;
			}
		} 
	  } 
	}
	
	function submit_admin(){
		$('#admin').submit();
	}