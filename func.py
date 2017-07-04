import datetime

def timetostr(timestamp, dtfmt):
    return datetime.datetime.fromtimestamp(timestamp).strftime(dtfmt)
