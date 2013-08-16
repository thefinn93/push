coderequest = new XMLHttpRequest();
devicerequest = new XMLHttpRequest();
devicecheck = null;
oldlist = null;
testnotifyxhr = new XMLHttpRequest();

function testnotify(id) {
    document.getElementById(id).disabled = true;
    testnotifyxhr = new XMLHttpRequest();
    testnotifyxhr.onload = testnotifycb;
    testnotifyxhr.open("GET","devices?action=test&id=" + id + "&nocsrf=" + NoCSRF);
    testnotifyxhr.send(null)
}

function testnotifycb() {
    result = JSON.parse(testnotifyxhr.responseText);
    NoCSRF = result['nocsrf']
    document.getElementById(result['result']).disabled = false;
}

function pairdevice() {
    coderequest = new XMLHttpRequest();
    coderequest.onload = showQR;
    coderequest.open("GET","devices?action=makecode&nocsrf=" + NoCSRF, true);
    coderequest.send(null);
}

function updateDeviceList() {
    devicerequest = new XMLHttpRequest();
    devicerequest.onload = showDeviceList;
    devicerequest.open("GET", "devices?action=listdevices&nocsrf=" + NoCSRF, true);
    devicerequest.send(null);
}

function showDeviceList() {
    result = JSON.parse(devicerequest.responseText);
    NoCSRF = result['nocsrf']
    document.getElementById("devicelist").innerHTML = result['result'];
    if(oldlist != null) { 
        if(oldlist.length < devicerequest.responseText.length) {
            document.getElementById("pairingbox").style.display = "none";
            clearInterval(devicecheck);
        }
    }
    oldlist = devicerequest.responseText;
}

function showQR() {
    result = JSON.parse(coderequest.responseText);
    NoCSRF = result['nocsrf']
    var qr = qrcode(4, 'L');
    qr.addData(result['result']);
    qr.make();
    document.getElementById("qr").innerHTML = qr.createImgTag(7);
    document.getElementById("pairingbox").style.display = "block";
    devicecheck = setInterval(updateDeviceList, 1000);
}
window.onload = updateDeviceList;
