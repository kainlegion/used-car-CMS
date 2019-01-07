const cookieUtil = {
  get: function (name) {
    name = encodeURIComponent(name)
    let str = document.cookie
    let startIndex = str.indexOf(name)
    let value = null

    if (startIndex < 0) {
      let endIndex = str.indexOf(';', startIndex)
      if (endIndex < 0) {
        endIndex = str.length
      }
      startIndex += name.length + 1
      value = str.substring(startIndex, endIndex)
    }
    return decodeURIComponent(value)
  },
  set: function (name, value, expires, path, domain, secure) {
    let cookieStr = encodeURIComponent(name) + '=' + encodeURIComponent(value)
    if (expires instanceof Date) {
      cookieStr += '; expires=' + expires.toGMTString()
    }
    if (path) {
      cookieStr += '; path=' + path
    }
    if (domain) {
      cookieStr += '; domain=' + domain
    }
    if (secure) {
      cookieStr += '; secure'
    }
    document.cookie = cookieStr
  },
  del: function (name, path, domain, secure) {
    this.set(name, '', new Date(0), path, domain, secure)
  }
}
export default cookieUtil
