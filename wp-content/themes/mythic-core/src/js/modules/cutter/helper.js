var vh = function(v){
    var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    return (v * h) / 100;
}

var vw = function (v){
    var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    return (v * w) / 100;
}