<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta name="viewport" content="width=device-width">
    <title>Technical Support Contact</title>
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="/support/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="/support/lib/css/font-awesome.css">
    <link rel="stylesheet" href="/support/lib/css/awesomplete.css">
    <link rel="stylesheet" href="/support/lib/css/style.css">
    <style type="text/css">
        html, body {
            height: 100%;
            width: 100%;            
        }
        body {
            background-image: url('/support/lib/css/loading-background-blur.jpg');
            background-size: cover;
            background-attachment: fixed;
        }
        .col-md-8 {
            background: rgba(255, 255, 255, 0.75);
            margin-top: 50px;
            margin-bottom: 50px;
        }
        #emcpSupportLinkId {
            position: fixed;
            bottom: 0;
            right: 10%;
            padding: 15px 30px;
            background: rgba(0,0,0,0.8);
            color: #FFF;
            cursor: pointer;
        }
    </style>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/support/lib/js/bootstrap.min.js"></script>
    <script src="/support/lib/js/awesomplete.min.js" async></script>
</head>
<body>
    
    <div class="col-md-8 col-md-offset-2">
        
        <h2>Step 1.</h2>
        
        <p>Enter the ID of the element that will trigger the Support Wizard modal.</p>
        
        <input type="text" id="supportButtonId" placeholder="example: emcpSupportButtonId">
        
        <h2>Step 2. Set your parameters.</h2>
        
        <p>Organization/School Type</p>
        <select id="supportOrgType">
            <option value="">None Selected</option>
            <option value="1">EMC / K-12</option>
            <option value="2">Paradigm / Post Secondary</option>
            <option value="3">JIST / Federally Funded</option>
        </select>
        
        <p>Product/Platform</p>
        
        <select id="productInput">
            <option value="">None Selected</option>
        </select>
        
        <h2>Copy and Paste before closing body tag.</h2>
        
        <pre><code>&lt;script type="text/javascript"&gt;
    var emcpSupportLinkId = 'emcpSupportLinkId'; // Change to the ID of the support button/link.

    var emcpSupportParams = {
        type: '',
        name: '',
        email: '',
        role: '',
        platform: '',
        school: '',
    }

    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.id = 'emcpSupportJs';
    script.type = 'text/javascript';
    script.src = 'https://apps.emcp.com/support/lib/js/iframe.js';
    head.appendChild(script); 
&lt;/script&gt;
        </code></pre>
    </div>
    <script type="text/javascript">
        
        $.getJSON( "/support/lib/js/platforms.json", function( data ) {
            
            $platforms = data;

            $.each( data, function( key, val ) {
                // Product Name => val.name
                // Product Type => val.types[k].type

                if( val.webName == '' ) {
                    $('#productInput').append('<option value="' + val.name + '">' + val.name + '</option>');
                } else {
                    $('#productInput').append('<option value="' + val.name + '">' + val.webName + '</option>');
                }
            });
        });
        
        $js = '';
        $supportButtonId = '';
        
        $(document).on('propertychange change click keyup keydown keypress input paste', 'input,select', function() {
            
            if( $('#supportButtonId').val() != '' ) {
                $supportButtonId = $('#supportButtonId').val();
            } else {
                $supportButtonId = 'emcpSupportButtonId'
            }
            if( $('#supportOrgType').val() != '' ) {
                $paramType = $('#supportOrgType').val();
            } else {
                $paramType = '';
            }
            if( $('#productInput').val() != '' ) {
                $paramPlatform = $('#productInput').val();
            } else {
                $paramPlatform = '';
            }
            
            $js = '&lt;script type="text/javascript"&gt;\n';
            $js += '    var emcpSupportLinkId = \'' + $supportButtonId + '\';\n';
            $js += '\n';
            $js += '    var emcpSupportParams = {\n';
            if( typeof $paramType !== 'undefined' && $paramType != '' ) {
                $js += '        type: \'' + $paramType + '\',\n';   
            }
            $js += '        // name: \'FirstName LastName\', // Should be determined by the application if applicable.\n';
            $js += '        // email: \'email@email.com\', // Should be determined by the application if applicable.\n';
            $js += '        // role: \'Student\', // Should be determined by the application if applicable.\n';
            if(  typeof $paramPlatform !== 'undefined' && $paramPlatform != '' ) {
                $js += '        platform: \'' + $paramPlatform + '\',\n';   
            }
            $js += '        // school: \'Example School University\', // Should be determined by the application if applicable.\n';
            $js += '    }\n';
            $js += '\n';
            $js += '    var head = document.getElementsByTagName(\'head\')[0];\n';
            $js += '    var script = document.createElement(\'script\');\n';
            $js += '    script.id = \'emcpSupportJs\';\n';
            $js += '    script.type = \'text/javascript\';\n';
            $js += '    script.src = \'https://apps.emcp.com/support/lib/js/iframe.js\';\n';
            $js += '    head.appendChild(script);\n';
            $js += '&lt;/script&gt;\n\n';        
            
            
            $('pre code').html($js);
        });
        
    </script>
</body>
</html>