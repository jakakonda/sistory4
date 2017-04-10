<!DOCTYPE html>
<html lang="sl" xml:lang="sl" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <link rel="icon" type="image/png" href="/img/icon.png">
    <title>Sistory 4</title>

    <script src="/lib/jquery/jquery-2.1.1.js"></script>
    <script src="/lib/jquery/jquery.zclip.js"></script>
    <script src="/lib/codemirror/codemirror.js"></script>
    <script src="/lib/codemirror/codemirror.mode.xml.js"></script>

    <script src="/js/si4/si4.js"></script>
    <script src="/js/si4/config.js"></script>
    <script src="/js/si4/util.js"></script>
    <script src="/js/si4/codes.js"></script>
    <script src="/js/si4/lookup.js"></script>
    <script src="/js/si4/api.js"></script>
    <script src="/js/si4/object/si4EventBase.js"></script>
    <script src="/js/si4/object/si4FileUploader.js"></script>
    <script src="/js/si4/widget/si4Element.js"></script>
    <script src="/js/si4/widget/si4TabPage.js"></script>
    <script src="/js/si4/widget/si4DataTable.js"></script>
    <script src="/js/si4/widget/si4Panel.js"></script>
    <script src="/js/si4/widget/si4Form.js"></script>
    <script src="/js/si4/widget/si4Input.js"></script>
    <script src="/js/si4/widget/si4InputArray.js"></script>
    <script src="/js/si4/widget/si4MultiSelect.js"></script>
    <script src="/js/si4/widget/si4Dialog.js"></script>
    <script src="/js/si4/widget/si4HtmlTable.js"></script>
    <script src="/js/si4/widget/si4Hint.js"></script>
    <script src="/js/si4/widget/si4AutoComplete.js"></script>
    <script src="/js/si4/hint.js"></script>

    <link rel="stylesheet" href="/lib/zurb/css/normalize.css" />
    <link rel="stylesheet" href="/lib/zurb/css/foundation.css" />
    <link rel="stylesheet" href="/lib/codemirror/codemirror.css">

    <link rel="stylesheet" href="/css/gradients.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/standard.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/si4.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/modules.css" type="text/css" media="screen" />
</head>

<body>
<div id="header" class="">
    <div class="inline vtop">
        <a href="/" title="Slovenian Index of citation - Admin"><img src="img/logo2.png" class="logoImage"/>
            <img src="/img/loading-book.gif" class="loadingGif" id="loadingGif" style="display:none;">
            <div class="mainTitle">Sistory 4</div>
        </a>
    </div>
    <img src="img/loading4.gif" class="loadingGif2" id="loadingGif2" style="display:none;" />

    <div class="inline vtop">

        <div id="initLoader" class="initLoader">Loading codes...</div>

        <div id="navigation" class="navigation" style="display:none;">
            <div>
                <ul class="navigationUl">

                    <li class="mainMenuList">
                        <a href="javascript:si4.loadModule({moduleName:'Dev/TestPage', newTab:'TestPage'});">DevTestPage</a>
                    </li>

                    <li class="mainMenuList">
                        <a href="javascript:si4.loadModule({moduleName:'System/Dashboard', newTab:'System'});">System</a>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div class="floatRight vtop identityDiv">
        <span class="loginName">Username (<a href="/logout">Logout</a>)</span>
        <a href="/login">Login</a>
    </div>

</div>

<div id="content">
    <div id="pageHolder"></div>
    <div id="primaryPage">
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#initLoader').css("display", "none");
        $('#navigation').fadeIn(si4.defaults.fadeTime);
    });
</script>

</body>
</html>
