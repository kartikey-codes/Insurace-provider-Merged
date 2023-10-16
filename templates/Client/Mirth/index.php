<!-- File: templates/Mirth/index.ctp -->

<!DOCTYPE html>
<html>
<head>
   
</head>
<body>
    <h3>Click to connect with Mirth</h3>
    <button id="connectButton">Connect with Mirth</button>

    <div id="connectionStatus"></div>
    <div id="contentContainer"></div>

    <!-- JavaScript -->
    <script>
        document.getElementById('connectButton').addEventListener('click', function() {
            document.getElementById('connectionStatus').innerHTML = "<h3>Connection established with Mirth</h3>";
            
            var getDetailsButton = document.createElement("button");
            getDetailsButton.innerHTML = "Click to get patient details";
            getDetailsButton.addEventListener('click', function() {
                var content = '<h3>Patient Information</h3>'+'Account: MF0050356/15'+'<br>'+'ID: AND234DA_PID3, ABC123DF'+'<br>'+'Name: Patlast, Patfirst Mid'+'<br>'+'DOB: February 2, 1967'+'<br>'+'Phone:222-555-8484'+'<br>'+'<h3>Visit Information</h3>'+'Admit Date: November 7, 2014 14:40'+'<br>'+'Location: MYFACSOMPL MYFAC'+'<br>'+'Attending Provider: Xavarie, SonnaCustom ID:XAVS'+'<br>'+'<h3>Order Request Information</h3>'+'Specimen: PT1311:H00001R<br>'+'Service: LAB<br>'+'Test Info:   Test ID:301.010057021-8 Test Name: Complete Blood Count (CBC)<br>'+'Test Date/Time: November 13, 2014 09:14 <br> Results Date/Time: November 13, 2014 09:15';
                document.getElementById('contentContainer').innerHTML = content;
            });

            document.getElementById('contentContainer').appendChild(getDetailsButton);
        });
    </script>
</body>
</html>
