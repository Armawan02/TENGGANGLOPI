<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A problem has been detected</title>
    <style>
        body {
            background-color: #0000aa;
            color: #ffffff;
            font-family: "Lucida Console", Monaco, monospace;
            font-size: 16px;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 20px;
        }
        ::selection {
            background: #ffffff;
            color: #0000aa;
        }
    </style>
</head>
<body>
    <div class="header">
        A problem has been detected and windows has been shut down to prevent damage<br>
        to your computer.
    </div>
    
    <div class="info">
        UNAUTHORIZED_SUPERADMIN_ACCESS_ATTEMPT
    </div>
    
    <div class="info">
        If this is the first time you've seen this stop error screen,<br>
        restart your computer. If this screen appears again, follow<br>
        these steps:
    </div>

    <div class="info">
        Check to make sure any new hardware or software is properly installed.<br>
        If this is a new installation, ask your hardware or software manufacturer<br>
        for any windows updates you might need.
    </div>
    
    <div class="info">
        If problems continue, disable or remove any newly installed hardware<br>
        or software. Disable BIOS memory options such as caching or shadowing.<br>
        If you need to use Safe Mode to remove or disable components, restart<br>
        your computer, press F8 to select Advanced Startup Options, and then<br>
        select Safe Mode.
    </div>
    
    <div class="info">
        Technical information:
    </div>
    
    <div class="info">
        *** STOP: 0x000000FE (0x00000008, 0x000000006, 0x00000009, 0x847075CC)
    </div>

    <div class="info">
        <a href="{{ route('register') }}" style="color: #ffffff; text-decoration: underline;">Click here to return to Registration...</a>
    </div>
</body>
</html>
