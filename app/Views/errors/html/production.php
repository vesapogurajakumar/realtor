<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?= lang('Errors.whoops') ?> | Vesta Real Estate</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --navy:#0A1628; --gold:#C9A84C; }
        * { box-sizing:border-box; margin:0; }
        body { font-family:'Inter',system-ui,sans-serif; background:var(--navy); color:#fff; min-height:100vh; display:grid; place-items:center; text-align:center; padding:2rem; }
        .badge { width:84px; height:84px; border-radius:50%; background:rgba(201,168,76,.15); color:var(--gold); display:grid; place-items:center; margin:0 auto 1.6rem; font-size:2.4rem; }
        h1 { font-family:'Playfair Display',serif; font-size:clamp(1.8rem,5vw,2.6rem); }
        p { color:rgba(255,255,255,.72); margin-top:1rem; max-width:460px; }
        a { display:inline-block; margin-top:2rem; background:var(--gold); color:var(--navy); font-weight:600; text-decoration:none; padding:.9rem 2rem; border-radius:999px; }
        a:hover { background:#E0C87B; }
        .brand { font-family:'Playfair Display',serif; font-weight:700; font-size:1.4rem; margin-bottom:2.5rem; }
        .brand b { color:var(--gold); }
    </style>
</head>
<body>
    <div>
        <div class="brand">Ves<b>ta</b></div>
        <div class="badge">&#9888;</div>
        <h1><?= lang('Errors.whoops') ?></h1>
        <p><?= lang('Errors.weHitASnag') ?> Our team has been notified. Please try again in a moment.</p>
        <a href="/">Back to Safety</a>
    </div>
</body>
</html>
