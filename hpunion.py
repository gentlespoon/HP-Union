#encoding=utf-8

# Flask Framework
from flask import Flask, request, session, g, redirect, url_for, abort, render_template, flash
app = Flask(__name__) # create the application instance
app.config.from_object(__name__) # load config from this file
# load default config and override config from an environment variable
app.config.update(dict(SECRET_KEY="hpuniondevkey"))
app.config.from_envvar("HPUNION_SETTINGS", silent=True)


import os
from pprint import pprint
# import pymysql
import sys
import datetime

try:
    from urllib.parse import urlparse # for python2 compatibility
except ImportError:
    from urlparse import urlparse


# HP-Union imports
from config import *
from func import *
from lang import lang




""" ====================================================== """


print("\n\n==== Python Version ====\n" + sys.version)
print("\n==== Server Started ====\n")


# at the end of each request
@app.teardown_appcontext
def teardown(error):
    dbclose(g)













# route

@app.route('/version')
def showversion():
    mtime = os.path.getmtime(__name__+".py")
    return timetostr(mtime, config.dtfmt['iso'])



@app.route('/')
def index():
    # Establish database connection
    time = now()
    print(time)

    cur = dbconn(g)
    cur.execute("SELECT uid, username, regdate FROM pre_common_member ORDER BY uid ASC")
    # cur.execute("update pre_ucenter_members set regdate=regdate+28800")
    data = cur.fetchall()
    print(data)
    for row in data:
        # pass
        row['regdate'] = timetostr(row['regdate'], dtfmt['iso'])
    return render_template('forum.html',
        title = lang['sitename'],
        bodytext = "Discuz! X3.3 UTF-8去死吧。",
        data = data,
        )
