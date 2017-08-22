var link = document.createElement('link');
link.id = 'emcpSupportCss';
link.rel = 'stylesheet';
link.type = 'text/css';
link.href = 'https://apps.emcp.com/support/lib/css/iframe.css';
link.media = 'all';
head.appendChild(link);

var jsUrl = 'https://apps.emcp.com/support/';
var jsParams = '?modal=true';
var iframe = document.createElement('iframe');
iframe.id = 'emcpSupportIframe';
iframe.style.display = 'none';

var wizardButton = document.getElementById(emcpSupportLinkId);
wizardButton.onclick = function(event) {
    launchWizard(event);
}

function launchWizard(e) {
    
    e.preventDefault();
    
    var element =  document.getElementById('emcpSupportIframe');
    if (typeof(element) != 'undefined' && element != null) {
        //
    } else {
        if( typeof emcpSupportParams.type !== 'undefined' && emcpSupportParams.type != '' && typeof emcpSupportParams.type != 'object' ) { jsParams += '&type=' + emcpSupportParams.type }
        if( typeof emcpSupportParams.name !== 'undefined' && emcpSupportParams.name != '' && typeof emcpSupportParams.name != 'object' ) { jsParams += '&name=' + emcpSupportParams.name }
        if( typeof emcpSupportParams.email !== 'undefined' && emcpSupportParams.email != '' && typeof emcpSupportParams.email != 'object' ) { jsParams += '&email=' + emcpSupportParams.email }
        if( typeof emcpSupportParams.role !== 'undefined' && emcpSupportParams.role != '' && typeof emcpSupportParams.role != 'object' ) { jsParams += '&role=' + emcpSupportParams.role }
        if( typeof emcpSupportParams.platform !== 'undefined' && emcpSupportParams.platform != '' && typeof emcpSupportParams.platform != 'object' ) { jsParams += '&platform=' + emcpSupportParams.platform }
        if( typeof emcpSupportParams.school !== 'undefined' && emcpSupportParams.school != '' && typeof emcpSupportParams.school != 'object' ) { jsParams += '&school=' + emcpSupportParams.school }
        iframe.src = jsUrl + encodeURI(jsParams);
        
        document.body.appendChild(iframe);        
    }
    document.body.className += ' emcpSupportFixed';
    _('emcpSupportIframe').fade('in', 500);  
}
function closeWizard() {
    document.body.classList.remove('emcpSupportFixed');
    _('emcpSupportIframe').fade('out', 500);
}

function _(el) {
  if (!(this instanceof _)) {
    return new _(el);
  }
  this.el = document.getElementById(el);
}

_.prototype.fade = function fade(type, ms) {
    var isIn = type === 'in',
        opacity = isIn ? 0 : 1,
        interval = 50,
        duration = ms,
        gap = interval / duration,
        self = this;

    if(isIn) {
        self.el.style.display = 'inline';
        self.el.style.opacity = opacity;
    }

    function func() {
        opacity = isIn ? opacity + gap : opacity - gap;
        self.el.style.opacity = opacity;

        if(opacity <= 0) self.el.style.display = 'none'
        if(opacity <= 0 || opacity >= 1) window.clearInterval(fading);
    }

    var fading = window.setInterval(func, interval);
}


        
window.addEventListener('message', function(event) { 
    if (~event.origin.indexOf('https://apps.emcp.com')) { 
        if( event.data == 'close' ) { closeWizard(); }
    } else {  return; } 
});        
