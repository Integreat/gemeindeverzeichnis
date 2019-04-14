$('#searchtext').keypress(function (e) {
    var key = e.which;
    if(key == 13) {
        setUrl("search");
    }
});
function evalUrl () {
    var parts = window.location.pathname.split("/");
    if(parts[2] == "search") {
        search(parts[3]);
    }else if(parts[2] == "details"){
        details(parts[3]);
    }else{}
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
        searchhtml = '<table class="table table-striped">';
        for (item of data) {
            searchhtml = searchhtml + "<thead><tr><th scope='col' colspan='2'><h4 class='text-center'>"+item['name']+"</h4></th></tr></thead>" +
            "<tr><th scope='row'>Regierungsbezirk</th><td>"+item['district']+"</td></tr>" +
            "<tr><th scope='row'>Bundesland</th><td>"+item['state']+"</td></tr>" +
            "<tr><th scope='row'>Landkreis</th><td>"+item['county']+"</td></tr>" +
            "<tr><th scope='row'>Gemeindetyp</th><td>"+item['type']+"</td></tr>" +
            "<tr><th scope='row'>Anschrift</th><td>"+item['address']['recipient']+", "+item['address']['street']+", "+item['address']['zip']+" "+item['address']['city']+"</td></tr>" +
            "<tr><th scope='row'>L&auml;ngengrad</th><td>"+item['longitude']+"</td></tr>" +
            "<tr><th scope='row'>Breitengrad</th><td>"+item['latitude']+"</td></tr>" +
            "<tr><th scope='row'>Bevölkerung</th><td>"+item['population']+"</td></tr>";
        };
        searchhtml = searchhtml + "</table>";
        $("#result").html(searchhtml);
    });
}