# Gemeindeverzeichnis
Due to our desire for Open Data, we create a machine readable API to the Gemeindeverzeichnis of the Statistisches Bundesamt (https://www.destatis.de/DE/Themen/Laender-Regionen/Regionales/Gemeindeverzeichnis/_inhalt.html).

# CURL examples

## Details view
This requires the "Gemeindeschlüssel".
````
curl --header "Content-Type: application/json" \
--request POST \
--data '["09362000"]' \
"https://gvz.integreat-app.de/api/details/"

````
or
````
https://gvz.integreat-app.de/api/details/09362000
````

## Search
Search terms can be the name of the location or zip code.
````
curl --header "Content-Type: application/json" \
--request POST \
--data '["Wangen"]' \
"https://gvz.integreat-app.de/api/search/"
````
or
````
curl https://gvz.integreat-app.de/api/search/Wangen
````

## Quick Search
A minimalistic search is also available. This is intended to make suggestions for completion of zip codes or city names.
````
curl --header "Content-Type: application/json" \
--request POST \
--data '["Wangen"]' \
https://gvz.integreat-app.de/api/quicksearch/
````
or
````
curl https://gvz.integreat-app.de/api/quicksearch/Wangen
````

## Search by county
Search by county name
````
curl --header "Content-Type: application/json" \
--request POST \
--data '["Ravensburg"]' \
https://gvz.integreat-app.de/api/searchcounty/
````
or
````
curl https://gvz.integreat-app.de/api/searchcounty/Ravensburg
````
