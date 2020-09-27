vcl 4.0;
backend default {
  .host = "nginx";
  .port = "8080";
}
import std;

acl invalidators {
    "localhost";
    "php-fpm";
    "nginx";
}

sub vcl_recv {
    # return (pass);
    # only cache GET requests
    if (req.method != "GET" && req.method != "HEAD" && req.method != "PURGE" && req.method != "BAN") {
        return (pass);
    }

    if (req.method == "PURGE") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed: " + client.ip));
        }

        return (purge);
    }
    if (req.method == "BAN") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }

        if (req.http.X-cache-tags) {
            ban("obj.http.X-Cache-Tags ~ " + req.http.X-Cache-Tags);
            return (synth(200, "Banned tags"));
        }
        ban("obj.http.x-host ~ " + req.http.x-host
            + " && obj.http.x-url ~ " + req.http.x-url
            + " && obj.http.content-type ~ " + req.http.x-content-type
        );

        return (synth(200, "Banned"));
    }

    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }
    return (hash);
}

sub vcl_deliver {
  # Display hit/miss info
  if (obj.hits > 0) {
    set resp.http.V-Cache = "HIT";
  }
  else {
    set resp.http.V-Cache = "MISS";
  }

}
sub vcl_backend_response {
    # Set ban-lurker friendly custom headers.
    set beresp.http.X-Url = bereq.url;
    set beresp.http.X-Host = bereq.http.host;
    if (beresp.status == 200) {
        unset beresp.http.Cache-Control;
        set beresp.http.Cache-Control = "public; max-age=3600";
        set beresp.ttl = 3600s;
    }
    set beresp.http.Served-By = beresp.backend.name;
    set beresp.http.V-Cache-TTL = beresp.ttl;
    set beresp.http.V-Cache-Grace = beresp.grace;
}