let mybutton = document.getElementById("myBtn");
window.onscroll = function () { scrollFunction() };
//scroll top: vị trí hiện tại
function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
}

function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
//     navigation-search
function on_search() {
    document.getElementById("navigation_search").style.height = "100%";
}
function close_search() {
    document.getElementById("navigation_search").style.height = "0%";
}

function openNav() {
    document.getElementById("cart").style.right = "0px";
}
function closeNav() {
    document.getElementById("cart").style.right = "-370px";
}

