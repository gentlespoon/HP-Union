#encoding=utf-8

# Flask Framework
from flask import Flask, request, session, g, redirect, url_for, abort, render_template, flash
app = Flask(__name__) # create the application instance
app.config.from_object(__name__) # load config from this file
# load default config and override config from an environment variable
app.config.update(dict(SECRET_KEY="wow_this_is_so_damn_hard_to_guess"))
app.config.from_envvar("HPUNION_SETTINGS", silent=True)


import datetime
import os
from pprint import pprint
import random
import string
import sys

try:
    from urllib.parse import urlparse # for python2 compatibility
except ImportError:
    from urlparse import urlparse


# HP-Union imports
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
    path = os.path.abspath(__file__)
    mtime = os.path.getmtime(path)
    return timetostr(utz(mtime+dtfmt['svroffset']*-(3600)), dtfmt['iso'])



@app.route('/')
def home():
    # Establish database connection
    # time = now()

    db = dbconn(g)
    db.execute("SELECT uid, username, regdate FROM usertable ORDER BY uid ASC")
    # cur.execute("update pre_ucenter_members set regdate=regdate+28800")
    data = db.fetchall()
    # print(data)
    for row in data:
        # pass
        if (row['regdate'] != None):
            row['regdate'] = timetostr(utz(row['regdate']), dtfmt['iso'])
    return render_template('home.html',
        title = lang['site']['name'],
        body = "",
        data = data,
        lang = lang,
        )







@app.route('/member')
def member():
    if (not session.get('uid')):
        return render_template('member.html',
            title = lang['site']['name'],
            body = lang['member']['not-logged-in'],
            lang = lang,
            )


@app.route('/member/login', methods=['GET','POST'])
def login():
    if (session.get('uid')):
        abort(401) # do not allow re-auth

    body = ""
    data = {}
    if (request.values.get('username') and request.values.get('password')):
        db = dbconn(g)
        db.execute("SELECT uid, username, password, salt FROM usertable WHERE username=%s", (request.values.get('username')))
        data = db.fetchone()

        print(data)
        body = str(data)
    else:
        # if Username or Password empty
        body = lang['developing']
        return render_template('error.html',
            title = lang['site']['name'],
            body = body,
            lang = lang,
            )

    # return render_template('member.html',
    #     title = lang['site']['name'],
    #     body = body,
    #     data = data,
    #     lang = lang,
    #     )

@app.route('/member/register', methods=['GET','POST'])
def register():
    if (session.get('uid')):
        abort(401) # do not allow re-register

    if (request.method=="GET"):
        # display the register window
        pass
    data = ""
    body = ""

    if (request.method=="POST"):
        # register a new user
        submittedUsername = request.values.get('username')
        submittedPassword = request.values.get('password')

        if (submittedUsername and submittedPassword):
            # if Username and Password not empty
            db = dbconn(g)
            # Check if username is registered
            db.execute("SELECT uid FROM usertable WHERE username=%s", (request.form.get('username')))
            if (db.fetchone() != None):
                # Username already registered
                body = lang['member']['dup-username']
                return render_template('error.html',
                    title = lang['site']['name'],
                    body = body,
                    lang = lang,
                    )
            else:
                # Username available
                # generate random salt
                salt = ''.join(random.choices(string.ascii_lowercase + string.digits, k=6))
                # encrypt password
                encryptedPassword = pwdgen(submittedPassword, salt)
                # insert new user into database
                db.execute("INSERT INTO usertable (username, password, salt) VALUES (%s, %s, %s)", (submittedUsername, encryptedPassword, salt))
                return redirect(url_for('register'))

        else:
            # if Username or Password empty
            body = lang['member']['empty-field']
            return render_template('error.html',
                title = lang['site']['name'],
                body = body,
                lang = lang,
                )

    return render_template('register.html', body=body)
