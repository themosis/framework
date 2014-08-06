<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php wp_title(''); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
    <style type="text/css">
        body{
            background-color: #002832;
            margin: 0;
            padding: 0;
        }

        #app{
            width: 100%;
            padding: 6em 0;
            overflow: hidden;
        }

        .wrapper{
            width: 960px;
            margin: 0 auto;
        }

        p{
            display: inline-block;
            font-family: 'Open Sans', sans-serif;
            color: #666666;
            font-size: 16px;
            font-size: 1em;
            line-height: 100%;
            padding: 0.75em 1.5em;
            background-color: #ffffff;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            border: 1px solid #ffffff;
            margin-right: 0.325em;
        }

        .content{
            margin: 0 auto;
            text-align: center;
        }

        .content a, .content a:link, .content a:active, .content a:visited{
            display: inline-block;
            font-family: 'Open Sans', sans-serif;
            font-size: 16px;
            font-size: 1em;
            line-height: 100%;
            background-color: #00b49c;
            color: #ffffff;
            padding: 0.75em 1em;
            border: 1px solid #00b49c;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            text-decoration: none;
            -webkit-transition: background-color 0.3s ease, color 0.3s ease;
            -moz-transition: background-color 0.3s ease, color 0.3s ease;
            -o-transition: background-color 0.3s ease, color 0.3s ease;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .content a:hover{
            background-color: #002832;
            color: #00b49c;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body>
<div id="app">
    <div class="wrapper">
        <div class="content">
            <p>{{ __('No route defined for this request...', THEMOSIS_FRAMEWORK_TEXTDOMAIN) }}</p>
            <a href="{{ home_url() }}" title="{{ __('Back home', THEMOSIS_FRAMEWORK_TEXTDOMAIN) }}">{{ __('Back home', THEMOSIS_FRAMEWORK_TEXTDOMAIN) }}</a>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>