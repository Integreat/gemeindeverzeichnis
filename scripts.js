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
        searchhtml = "<div class='container'>";
        for (item of data) {
            searchhtml = searchhtml + "<div class='row'><div class='col'>"+item['name']+"</div></div>" +
            "<div class='row'><div class='col'>Regierungsbezirk</div><div class='col'>"+item['district']+"</div></div>" +
            "<div class='row'><div class='col'>Bundesland</div><div class='col'>"+item['state']+"</div></div>" +
            "<div class='row'><div class='col'>Landkreis</div><div class='col'>"+item['county']+"</div></div>" +
            "<div class='row'><div class='col'>Gemeindetyp</div><div class='col'>"+item['type']+"</div></div>" +
            "<div class='row'><div class='col'>Anschrift</div><div class='col'>"+item['address']['recipient']+", "+item['address']['street']+", "+item['address']['zip']+" "+item['address']['city']+"</div></div>" +
            "<div class='row'><div class='col'>L&auml;ngengrad</div><div class='col'>"+item['longitude']+"</div></div>" +
            "<div class='row'><div class='col'>Breitengrad</div><div class='col'>"+item['latitude']+"</div></div>" +
            "<div class='row'><div class='col'>Bevölkerung</div><div class='col'>"+item['population']+"</div></div>";
        };
        searchhtml = searchhtml + "</div>";
        $("#result").html(searchhtml);
    });
}