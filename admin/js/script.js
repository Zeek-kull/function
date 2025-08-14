// Ensure sidenav starts closed when page loads
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("sidenav").style.width = "0px";
});

function openside() {
  if (document.getElementById("sidenav").style.width == "0px") {
    document.getElementById("sidenav").style.width = "250px";
  }
}

function closeside() {
  document.getElementById("sidenav").style.width = "0px";
}
