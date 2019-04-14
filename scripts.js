$('#searchtext').keypress(function (e) {
    var key = e.which;
    if(key == 13) {
        setUrl("search");
    }
});
function evalUrl () {
    var parts = window.location.pathname.split("/");
    console.log(parts);
    if(parts[2] == "search") {
        console.log("Action: search.")
        search(parts[3]);
    }else if(parts[2] == "details"){
        console.log("Action: details");
        details(parts[3]);
    }else{
        console.log("No action.");
    }
};
$(document).ready(function() {
    $("#searchbutton").click(function(){
        setUrl("search");
    });
    evalUrl();
});
function setUrl(action, key) {
    if(action == "search") {
        key = $("#searchtext").val();
    }
    window.history.pushState("Foo", "Baz", "/ui/"+action+"/"+key);
    evalUrl();
};

function search( term ) {
    console.log("Searching for "+term);
    $.get("/api/search/"+term, function(data, status){
        var searchhtml;
        searchhtml = "<ul>";
        for (item of data) {
            searchhtml = searchhtml + "<li>"+item['name']+" ("+item['zip']+")<br>"+item['state']+" <a href='/ui/details/"+item['key']+"'>Details</a></li>";
        };
        searchhtml = searchhtml + "</ul>";
        $("#result").html(searchhtml);
    });
};
function details( key ) {
    $.get("/api/details/"+key, function(data, status){
        var searchhtml;
        searchhtml = "<ul>";
        console.log(data);
        for (item of data) {
            searchhtml = searchhtml + "<li><h2>"+item['name']+"</h2></li>" +
            "<li>Regierungsbezirk: "+item['district']+"</li>" +
            "<li>Bundesland: "+item['state']+"</li>" +
            "<li>Landkreis: "+item['county']+"</li>" +
            "<li>Gemeindetyp: "+item['type']+"</li>" +
            "<li>Anschrift: "+item['address']['recipient']+", "+item['address']['street']+", "+item['address']['zip']+" "+item['address']['city']+"</li>" +
            "<li>L&auml;ngengrad, Breitengrad: "+item['longitude']+", "+item['latitude']+"</li>" +
            "<li>Bevölkerung: "+item['population']+"</li>";
        };
        searchhtml = searchhtml + "</ul>";
        $("#result").html(searchhtml);
    });
}