from config import *
import datetime
import hashlib
import pymysql
import pytz
import re
import time

# class Time:
#     def __init__()
#
#

# User Time Zone

def timetostr(timestamp, fmt):
    # Return human-readable time in user timezone (defined in config.py)
    return datetime.datetime.fromtimestamp(timestamp, pytz.timezone(dtfmt['usrtz'])).strftime(fmt)


def now():
    # Return unix time stamp (UTC)
    return int(datetime.datetime.strptime(str(datetime.datetime.now()), "%Y-%m-%d %H:%M:%S.%f").timestamp())






# Request functions
def geturl(request):
    return urlparse(request.url)



# Database functions
def dbconn(g):
    g.conn = pymysql.connect(
        host=dbconf['host'],
        port=dbconf['port'],
        user=dbconf['user'],
        passwd=dbconf['pass'],
        db=dbconf['dtbs'],
        charset=dbconf['char'],
        )
    print("MySQL Connected")
    return g.conn.cursor(pymysql.cursors.DictCursor)

def dbclose(g):
    # Close database connection
    if hasattr(g, 'conn'):
        g.conn.close()
        print("MySQL Closed")


# Hash functions
def md5encode(str):
    return hashlib.md5(str.encode()).hexdigest()

# Member functions
def pwdgen(pwd, salt):
    return md5encode(md5encode(pwd)+salt)

def checkUsername(username):
    match = re.search('\s\!' , username)
    return match
