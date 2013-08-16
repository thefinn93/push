var xhr = null;

function xhrcb() {
    response = JSON.parse(xhr.responseText);
    NoCSRF = response['token'];
    out = "";
    for(i = 0; i < response['result'].length; i++) {
        out += "<tr><td><b>" + response['result'][i]['name'] + "</b></td><td><a href=\"javascript: void(0);\" onclick=\"rename('" + response['result'][i]['token'] + "', '" + response['result'][i]['name'] + "')\">rename</a></td><td><a href=\"javascript: void(0);\" onclick=\"remove('" + response['result'][i]['token'] + "')\">remove</a></td><td><code>" + response['result'][i]['token'] + "</code></td></tr>";
    }
    document.getElementById("applist").innerHTML = out;
}

function addapp() {
    xhr = new XMLHttpRequest;
    name = prompt("Please name this app", appname);
    xhr.onload = xhrcb;
    xhr.open("GET", "apps?action=add&name=" + name + "&nocsrf=" + NoCSRF, true);
    xhr.send(null)
}

function remove(token) {
    xhr = new XMLHttpRequest;
    xhr.onload = xhrcb;
    xhr.open("GET", "apps?action=remove&token=" + token + "&nocsrf=" + NoCSRF, true);
    xhr.send(null)
}

function rename(token, oldname) {
    xhr = new XMLHttpRequest;
    name = prompt("Please name this app", oldname);
    xhr.onload = xhrcb;
    xhr.open("GET", "apps?action=rename&name=" + name + "&token=" + token + "&nocsrf=" + NoCSRF, true);
    xhr.send(null)
}
