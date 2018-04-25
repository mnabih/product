
<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title>Template</title>

</head>
<body>

    <header>
        <div class="header" style="background:{{EmailColors()->email_header_color}}">

        </div>
    </header>

    <section>
        <div class="single">
            <div class="container">
                <div class="p" style="color:{{EmailColors()->email_font_color}}">
					{{$Message}}
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer" style="background:{{EmailColors()->email_footer_color}}">
        </div>
    </footer>

</body>
</html>
