function removeElementsByClass(className) {
    elements = document.getElementsByClassName(className);
    while (elements.length > 0) {
        elements[0].parentNode.removeChild(elements[0]);
    }
}
function rmvAds() {
    removeElementsByClass("cbalink");
    var element = document.getElementById("reklamacba");
    element.outerHTML = "";
    delete element;
}
if (window.addEventListener)
    window.addEventListener("load", rmvAds, false);
else if (window.attachEvent)
    window.attachEvent("onload", rmvAds);
else window.onload = rmvAds;