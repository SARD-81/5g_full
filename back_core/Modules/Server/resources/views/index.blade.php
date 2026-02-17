<html>
<head>
    <title>reCAPTCHA demo: Simple page</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<form action="{{ route('api/validation-reCaptcha') }}}" method="POST">
    <div class="g-recaptcha" data-sitekey="6LcSsSwrAAAAACr48sIuVQwBuBNjDiP8ksVqgkwa"></div>
    <br/>
    <input type="submit" value="Submit">
</form>


<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
<script type="text/javascript">
    var onloadCallback = function() {
        alert("grecaptcha is ready!");
    };
</script>


</body>
</html>
