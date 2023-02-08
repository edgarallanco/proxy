
## Webflow ➡️ Greenhouse Proxy

This repo provides a proxy to send application form data from Webflow to Greenhouse. The proxy works for both GET and POST methods.


### Instructions:
- url_encoded the proxy url
- Call the endpoint `/request?destination={proxy_url}`

You can view an example proxy here: [https://proxy.letter.run/api/request?destination=https%3A%2F%2Fgoogle.com](https://proxy.letter.run/api/request?destination=https%3A%2F%2Fgoogle.com)

--

### Example Javascript Code:

```
  var myHeaders = new Headers();
      myHeaders.append("Authorization", "Basic XXXXX");
      ...

  var formdata = new FormData();
      formdata.append(NAME, VALUE);
      ...
      
  var requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: formdata,
    redirect: "follow"
  };

  fetch("DOMAIN/api/request", requestOptions)
    .then((response) => response.text())
    .then((result) => {
        console.log("result", result)
    })
    .catch((error) => {
        console.log("error", error)
    });
```

### Setup
Learn more about running the Laravel PHP app here: [https://laravel.com/docs](https://laravel.com/docs)

--



<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>