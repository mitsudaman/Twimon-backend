
<!doctype html>
    <head>
        <title>ツイットモンスター</title>
        <meta property="og:title" content="ツイットモンスター | {{$user->name}}のせつめい">
        <meta property="og:image" content="{{$user->ogp_img_url}}">
        <meta property="og:description" content="{{$user->description1}} {{$user->description2}} {{$user->description3}}">
        <meta property="og:url" content="https://twimon.com">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="ツイットモンスター">
        <meta name="twitter:site" content="https://twimon.com">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="ツイットモンスター | {{$user->name}}のせつめい">
        <meta name="twitter:image" content="{{$user->ogp_img_url}}">
        <meta name="twitter:description" content="{{$user->description1}} {{$user->description2}} {{$user->description3}}">
    </head>
    <body>
        <script type="text/javascript">window.location="https://twimon.com/read/{{$user->id}}";</script>
    </body>
</html>
