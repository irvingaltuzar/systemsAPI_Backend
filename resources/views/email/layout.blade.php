<!DOCTYPE html>
<html lang="es">
    <body >
        <div style="padding-top:20px;">
            <div style="display: flex; flex-wrap: wrap;">
                <div style="width:25%;"></div>
                <div class="content">
                	<div class="header">
                        <img src="http://www.grupodmi.com.mx/intranet/img/grupo_DMI.jpg" style="width:160px;height:160px">
                    </div>
                    <div class="body">
                        <br>
                        @yield('body')
                        <br>
                        <br>
                    </div>
                    <div class="footer">
                        © {{now()->year}} Intranet - Grupo DMI. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </body>
    <style>
        body{
            font-family: sans-serif;
        }
        .content{
            width:50%; 
            box-shadow: rgba(194 194 194) 2px 4px 4px 4px;  
            border-radius:5px;
        }
        .header{
            text-align:center; 
            border-bottom:1px solid #3f5465; 
            padding-top:15px; 
            padding-bottom:15px;
        }
        .body{
            padding:15px;
        }
        .footer{
            padding:10px; 
            background: #3f5465; 
            border-radius:0px 0px 5px 5px;
            font-size: 12px;
            color:white;
            text-align: center;
        }
    </style>
</html>