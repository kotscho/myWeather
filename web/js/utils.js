var currentSelection = 0;

function setSelected(listItem) {
	var title = $("#searchInstant ul li a").eq(listItem).attr('title');
        
	$("#cityname").val(title);
        //console.log('Title is ' + title);
	$("#searchInstant ul li a").removeClass("search_hover");
	$("#searchInstant ul li a").eq(listItem).addClass("search_hover");
}