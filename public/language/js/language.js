$(document).ready(function () {
	$("#lang_changer").change(function(){
		var lang=$(this).val();
		var langLabel=$('#lang_changer option:selected').text();
		//confirm("Are you sure want to change your current language to "+langLabel+"?");
		var ajaxData={lang:lang};
		$.ajax({
			url:'./language/changeLanguage',
			type:"POST",
			async:true,
			data:ajaxData,
			success:function(res){
				if(res=='true')
				{
					window.location.href='./';
				}
			}
		}).error(function(){
			console.log("Something went wrong while changing Language");
		});
	})
});

