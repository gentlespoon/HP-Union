# HP-Union
This is the source of the HP-Union site.

## Required Python modules
pymysql, flask, urlparse

## Local Testing Server
```sh
$ sudo ./runserver
```

```sh
# Enable Flask debug mode
$ export FLASK_DEBUG=true         

# Specify Flask app path
$ export FLASK_APP=/overlay/svr/hp-union.com/hpunion.py

# Execute Flask on port 80, listen to all hosts
$ flask run --host=0.0.0.0 --port=80
```

## Keep in Mind
Since this project is to be run in an OpenWRT router, please keep the memory footprint as low as possible.
Make sure Flask debug is turned off in production server.
