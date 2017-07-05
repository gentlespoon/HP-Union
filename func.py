import datetime
import pymysql
from config import *

# class Time:
#     def __init__()
#
#


def timetostr(timestamp, dtformat):
    return datetime.datetime.fromtimestamp(timestamp+dtfmt['offset']*3600).strftime(dtformat)

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
