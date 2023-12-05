<!DOCTYPE html>
<html lang="en">
    <body >
        <div style="padding-top:20px;">
            <div style="display: flex; flex-wrap: wrap;">
                <div style="width:25%;"></div>
                <div style="width:50%; box-shadow: rgba(194 194 194) 2px 4px 4px 4px;  border-radius:5px;">
                	<div style="text-align:center; border-bottom:1px solid #3f5465; padding-top:15px; padding-bottom:15px;">
                        <img src="http://www.grupodmi.com.mx/intranet/img/grupo_DMI.jpg" style="width:160px;">
                    </div>
                    <div style="padding:15px;">
                    	<h4>Buen día {{ $content['name']}}.</h4>
                    	<p style="padding-bottom:10px;">{!! $content['content'] !!}.</p>
                    </div>
                    <div style="padding:10px; background: #3f5465; border-radius:0px 0px 5px 5px;">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>