#!/bin/python

from functools import wraps
from flask import Flask, request, Response

app = Flask(__name__)

def check_auth(username, password):
    """This function is called to check if a username /
    password combination is valid.
    """
    return username == 'admin' and password == 'secret'

def authenticate():
    """Sends a 401 response that enables basic auth"""
    return Response(
    'Could not verify your access level for that URL.\n'
    'You have to login with proper credentials', 401,
    {'WWW-Authenticate': 'Basic realm="Login Required"'})

def requires_auth(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        auth = request.authorization
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
        return f(*args, **kwargs)
    return decorated

@app.route("/", methods=['GET'])
@requires_auth
def hello():
    return "Hello World!"

@app.route("/nginx/sites-enabled", methods=['GET'])
@requires_auth
def hello():
    return "Sites Enabled"

@app.route("/nginx/sites-enabled/delete", methods=['POST'])
@requires_auth
def hello():
    return "Delete Sites Enabled"

@app.route("/nginx/sites-enabled/update", methods=['POST'])
@requires_auth
def hello():
    return "Update Sites Enabled"

@app.route("/nginx/restart", methods=['Get'])
@requires_auth
def hello():
    return "Restart nginx"

@app.route("/nginx/reload", methods=['Get'])
@requires_auth
def hello():
    return "Reload nginx"


if __name__ == '__main__':
    app.run(debug=True)