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
        searchhtml = "<h4 class='text-center'>Ergebnisse</h4><table class='table table-striped' style='width:100%;'>";
        searchhtml = searchhtml + "<thead><tr><th scope='col'>Ort</th><th>PLZ</th><th>Land</th><th>Link</th></tr></thead>";
        for (item of data) {
            searchhtml = searchhtml + "<tr><td>"+item['name']+"</td><td>"+item['zip']+"</td><td>"+item['state']+"</td><td><a href='/ui/details/"+item['key']+"'>Details</a></td></tr>";
        };
        searchhtml = searchhtml + "</ul>";
        $("#result").html(searchhtml);
    });
};
function details( key ) {
    $.get("/api/details/"+key, function(data, status){
        var searchhtml;
        searchhtml = "<table class='table table-striped' style='width:100%;'>";
        for (item of data) {
            searchhtml = searchhtml + "<thead><tr><th scope='col' colspan='2'><h4 class='text-center'>"+item['name']+"</h4></th></tr></thead>" +
            "<tr><th scope='row'>Gemeindeschl&uuml;ssel</th><td>"+item['key']+"</td></tr>" +
            "<tr><th scope='row'>Regierungsbezirk</th><td>"+item['district']+"</td></tr>" +
            "<tr><th scope='row'>Bundesland</th><td>"+item['state']+"</td></tr>" +
            "<tr><th scope='row'>Landkreis</th><td>"+item['county']+"</td></tr>" +
            "<tr><th scope='row'>Gemeindetyp</th><td>"+item['type']+"</td></tr>" +
            "<tr><th scope='row'>Anschrift</th><td>"+item['address']['recipient']+"<br>"+item['address']['street']+"<br>"+item['address']['zip']+" "+item['address']['city']+"</td></tr>" +
            "<tr><th scope='row'>Homepage</th><td>"+item['address']['website_default']+"</td></tr>" +
            "<tr><th scope='row'>L&auml;ngengrad</th><td>"+item['longitude']+"</td></tr>" +
            "<tr><th scope='row'>Breitengrad</th><td>"+item['latitude']+"</td></tr>" +
            "<tr><th scope='row'>Bevölkerung</th><td>"+item['population']+"</td></tr>" +
            "<tr><th scope='row'>Postleitzahlen</th><td>"+item['zip_codes'].join(',')+"</td></tr>";
        };
        searchhtml = searchhtml + "</table>";
        $("#result").html(searchhtml);
    });
}