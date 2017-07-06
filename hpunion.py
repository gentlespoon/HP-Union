#encoding=utf-8

# Flask Framework
from flask import Flask, request, session, g, redirect, url_for, abort, render_template, flash
app = Flask(__name__)                       # create the application instance
app.config.from_object(__name__)            # load config from this file
# load default config and override config from an environment variable
app.config.update(dict(SECRET_KEY="wow_this_is_so_damn_hard_to_guess"))



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
    return timetostr(mtime, dtfmt['iso'])



@app.route('/')
def home():
    # Establish database connection
    # time = now()

    db = dbconn(g)
    db.execute("SELECT uid, username, regdate FROM common_member ORDER BY uid ASC")
    # cur.execute("update pre_ucenter_members set regdate=regdate+28800")
    data = db.fetchall()
    # print(data)
    for row in data:
        # pass
        if (row['regdate'] != None):
            row['regdate'] = timetostr(row['regdate'], dtfmt['iso'])
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
    body = ""
    data = ""

    if (session.get('uid')):
        abort(401) # do not allow re-login

    if (request.method=="GET"):
        # display the login window
        pass

    if (request.method=="POST"):

        if (request.values.get('username') and request.values.get('password')):
            db = dbconn(g)
            db.execute("SELECT uid, username FROM common_member WHERE username=%s", (request.values.get('username')))
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

    return render_template('login.html',
        title = lang['site']['name'],
        body = body,
        lang = lang,
        )



@app.route('/member/register', methods=['GET','POST'])
def register():
    data = ""
    body = ""



    if (session.get('uid')):
        abort(401) # do not allow re-register

    if (request.method=="GET"):
        # display the register window
        pass


    if (request.method=="POST"):

        print(request.values.get('username'))
        # register a new user
        submittedUsername = request.values.get('username')
        submittedPassword = request.values.get('password')

        # match = checkUsername(submittedUsername)
        # if (match != None):
        #     return str(lang['member']['res-username-1']+str(match.group(0))+lang['member']['res-username-2'])
        # else:
        #     return redirect(url_for('register'))


        if (submittedUsername and submittedPassword):
            # if Username and Password not empty
            db = dbconn(g)
            # Check if username is registered
            db.execute("SELECT uid FROM common_member WHERE username=%s", (submittedUsername))
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
                try:
                    #   >=3.6
                    salt = ''.join(random.choices(string.ascii_lowercase + string.digits, k=6))
                except:
                    #   <3.6
                    salt = ''.join(random.choice(string.ascii_lowercase + string.digits) for _ in range(6))
                # encrypt password
                encryptedPassword = pwdgen(submittedPassword, salt)
                # insert new user into database
                db.execute("INSERT INTO common_member (username, password, salt, regdate) VALUES (%s, %s, %s, %s)", (submittedUsername, encryptedPassword, salt, now() ))
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
