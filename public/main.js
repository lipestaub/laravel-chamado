var onSubmit = function(token) { 
    document.getElementById("chamado").submit();
};
  
var onloadCallback = function() {
    grecaptcha.render('enviar', {
        'sitekey' : '6Ld44CIkAAAAABiCXJZTGd53hVha-RPLv11-hePa',
        'callback' : onSubmit
    });
};