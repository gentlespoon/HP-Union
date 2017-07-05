from config import *
import datetime
import hashlib
import pymysql

# class Time:
#     def __init__()
#
#

# User Time Zone
def utz(timestamp):
    return timestamp+dtfmt['offset']*3600

def timetostr(timestamp, dtformat):
    return datetime.datetime.fromtimestamp(timestamp).strftime(dtformat)

def now():
    return (datetime.datetime.utcnow()-datetime.datetime(1970,1,1)).total_seconds()




# URL functions
def geturl():
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
