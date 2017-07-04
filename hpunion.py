#encoding=utf-8
from flask import Flask, request, session, g, redirect, url_for, abort, render_template, flash

from pprint import pprint
import pymysql
import sys

try:
    from urllib.parse import urlparse # for python2 compatibility
except ImportError:
    from urlparse import urlparse


# HP-Union imports
import config

print("\n\n==== Python Version ====\n" + sys.version)
print("\n==== Server Started ====\n")

app = Flask(__name__) # create the application instance
app.config.from_object(__name__) # load config from this file

# load default config and override config from an environment variable
app.config.update(dict(SECRET_KEY="hpuniondevkey"))
app.config.from_envvar("HPUNION_SETTINGS", silent=True)



# URL related functions
def geturl():
    return urlparse(request.url)

# Database related functions
def dbconn():
    g.conn = pymysql.connect(
        host=config.db['host'],
        port=config.db['port'],
        user=config.db['user'],
        passwd=config.db['pass'],
        db=config.db['dtbs'],
        charset=config.db['char'],
        )
    print("MySQL Connected")
    return g.conn.cursor()


# at the end of each request
@app.teardown_appcontext
def teardown(error):
    # Close database connection
    if hasattr(g, 'conn'):
        g.conn.close()
        print("MySQL Closed")




# route
@app.route('/')
def index():
    # Establish database connection
    cur = dbconn()
    cur.execute("SELECT uid, username FROM pre_common_member ORDER BY uid ASC")
    data = []
    for row in cur:
        data.append(row)
    return render_template('forum.html',
        title = "Un!on-囯阮麿砝帅朕舍仝",
        bodytext = "Discuz! X3.3 UTF-8去死吧。",
        data = data,
        )

