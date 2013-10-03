To generate the database, just execute the db/updatedb script:

    cd db
    ./updatedb

This will generate the `db/ipdb.sqlite` database file with the geolocation info
to be used by the search script.

To get a JSON response for geolocation for an IP, just visit:

    http://script-location/index.php?ip=<ip-address>

Script parameters:

* `ip`: the IP address, it'll use the client's one if omitted;
