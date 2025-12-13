function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value) {
  document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
}

let count = parseInt(getCookie("reloadCount")) || 0;
count++;
setCookie("reloadCount", count);
document.write(`Reload count: ${count}`);
