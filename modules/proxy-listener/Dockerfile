FROM alpine:3.6
MAINTAINER Ilyas Deckers

LABEL maintainer "Dimitri G. <dev@dmgnx.net>"

COPY ./docker-entrypoint.sh /docker-entrypoint.sh

ENV NAXSI_VERSION=0.55.3 \
    NGINX_VERSION=1.13.5

RUN set -ex ; \
    gpg_keys=" \
        0xB0F4253373F8F6F510D42178520A9993A1C052F8 \
        251A28DE2685AED4 \
        " \
    ; \
    config=" \
        --prefix=/etc/nginx \
        --sbin-path=/usr/sbin/nginx \
        --modules-path=/usr/lib/nginx/modules \
        --conf-path=/etc/nginx/nginx.conf \
        --error-log-path=/var/log/nginx/error.log \
        --http-log-path=/var/log/nginx/access.log \
        --pid-path=/var/run/nginx.pid \
        --lock-path=/var/run/nginx.lock \
        --http-client-body-temp-path=/var/cache/nginx/client_temp \
        --http-proxy-temp-path=/var/cache/nginx/proxy_temp \
        --http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp \
        --http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp \
        --http-scgi-temp-path=/var/cache/nginx/scgi_temp \
        --user=nginx \
        --group=nginx \
        --add-module=/tmp/naxsi-$NAXSI_VERSION/naxsi_src/ \
        --with-http_ssl_module \
        --with-http_realip_module \
        --with-http_addition_module \
        --with-http_sub_module \
        --with-http_dav_module \
        --with-http_flv_module \
        --with-http_mp4_module \
        --with-http_gunzip_module \
        --with-http_gzip_static_module \
        --with-http_random_index_module \
        --with-http_secure_link_module \
        --with-http_stub_status_module \
        --with-http_auth_request_module \
        --with-http_xslt_module=dynamic \
        --with-http_image_filter_module=dynamic \
        --with-http_geoip_module=dynamic \
        --with-threads \
        --with-stream \
        --with-stream_ssl_module \
        --with-stream_ssl_preread_module \
        --with-stream_realip_module \
        --with-stream_geoip_module=dynamic \
        --with-http_slice_module \
        --with-mail \
        --with-mail_ssl_module \
        --with-compat \
        --with-file-aio \
        --with-http_v2_module \
        " \
    ; \
    \
    addgroup -S nginx ; \
    adduser \
        -D \
        -S \
        -h /var/cache/nginx \
        -s /sbin/nologin \
        -G nginx \
        nginx \
    ; \
    \
    apk add --no-cache --virtual .build-deps \
        curl \
        gcc \
        gd-dev \
        geoip-dev \
        gnupg \
        libc-dev \
        libxslt-dev \
        linux-headers \
        make \
        openssl-dev \
        pcre-dev \
        zlib-dev \
    ; \
    \
    cd /tmp ; \
    curl \
        -fSL \
        http://nginx.org/download/nginx-$NGINX_VERSION.tar.gz \
        -o nginx.tar.gz \
    ; \
    curl \
        -fSL \
        http://nginx.org/download/nginx-$NGINX_VERSION.tar.gz.asc \
        -o nginx.tar.gz.asc \
    ; \
    curl \
        -fSL \
        https://github.com/nbs-system/naxsi/archive/$NAXSI_VERSION.tar.gz \
        -o naxsi.tar.gz \
    ; \
    curl \
        -fSL \
        https://github.com/nbs-system/naxsi/releases/download/$NAXSI_VERSION/naxsi-$NAXSI_VERSION.tar.gz.asc \
        -o naxsi.tar.gz.asc \
    ; \
    tar -xzf naxsi.tar.gz ; \
    tar -xzf nginx.tar.gz ; \
    \
    rm \
        naxsi.tar.gz \
        nginx.tar.gz \
    ; \
    \
    cd /tmp/nginx-$NGINX_VERSION ; \
    ./configure $config ; \
    make -j$(getconf _NPROCESSORS_ONLN) ; \
    make install ; \
    rm -rf /etc/nginx/html/ ; \
    mkdir /etc/nginx/conf.d/ ; \
    mkdir -p /usr/share/nginx/html/ ; \
    install -m644 \
        ../naxsi-$NAXSI_VERSION/naxsi_config/naxsi_core.rules \
        /etc/nginx \
    ; \
    install -m644 html/index.html /usr/share/nginx/html/ ; \
    install -m644 html/50x.html /usr/share/nginx/html/ ; \
    ln -s ../../usr/lib/nginx/modules /etc/nginx/modules ; \
    strip /usr/sbin/nginx* ; \
    strip /usr/lib/nginx/modules/*.so ; \
    \
    rm -rf \
        /tmp/naxsi-$NAXSI_VERSION \
        /tmp/nginx-$NGINX_VERSION \
    ; \
    \
    apk add --no-cache --virtual .build-deps gettext ; \
    mv /usr/bin/envsubst /tmp/ ; \
    \
    run_deps="$( \
        scanelf \
                --needed \
                --nobanner \
                /usr/sbin/nginx \
                /usr/lib/nginx/modules/*.so \
                /tmp/envsubst \
            | awk '{ gsub(/,/, "\nso:", $2); print "so:" $2 }' \
            | sort -u \
            | xargs -r apk info --installed \
            | sort -u \
        )" \
    ; \
    apk add --no-cache --virtual .nginx-run-deps $run_deps ; \
    apk del .build-deps ; \
    mv /tmp/envsubst /usr/local/bin/ ; \
    \
    ln -sf /dev/stdout /var/log/nginx/access.log ; \
    ln -sf /dev/stderr /var/log/nginx/error.log ; \
    mkdir -p /var/www/letsencrypt

RUN apk add --no-cache zlib-dev wget

RUN apk add --no-cache bash expat ffmpeg-libs libcrypto1.0 libstdc++ sudo && \
    ln -s /usr/lib/libcrypto.so.1.0.0 /usr/lib/libcrypto.so.10 && \
    ln -s /usr/lib/libssl.so.1.0.0 /usr/lib/libssl.so.10 && \
    ln -s /usr/lib/libtls.so.1.0.0 /usr/lib/libtls.so.10 && \
    ln -s /lib/libcrypto.so.1.0.0 /lib/libcrypto.so.10 && \
    ln -s /lib/libssl.so.1.0.0 /lib/libssl.so.10
    
RUN ALPINE_GLIBC_BASE_URL="https://github.com/sgerrand/alpine-pkg-glibc/releases/download" && \
    ALPINE_GLIBC_PACKAGE_VERSION="2.25-r0" && \
    ALPINE_GLIBC_BASE_PACKAGE_FILENAME="glibc-$ALPINE_GLIBC_PACKAGE_VERSION.apk" && \
    ALPINE_GLIBC_BIN_PACKAGE_FILENAME="glibc-bin-$ALPINE_GLIBC_PACKAGE_VERSION.apk" && \
    ALPINE_GLIBC_I18N_PACKAGE_FILENAME="glibc-i18n-$ALPINE_GLIBC_PACKAGE_VERSION.apk" && \
    apk add --no-cache --virtual=.build-dependencies wget ca-certificates && \
    wget \
        "https://raw.githubusercontent.com/andyshinn/alpine-pkg-glibc/master/sgerrand.rsa.pub" \
        -O "/etc/apk/keys/sgerrand.rsa.pub" && \
    wget \
        "$ALPINE_GLIBC_BASE_URL/$ALPINE_GLIBC_PACKAGE_VERSION/$ALPINE_GLIBC_BASE_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_BASE_URL/$ALPINE_GLIBC_PACKAGE_VERSION/$ALPINE_GLIBC_BIN_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_BASE_URL/$ALPINE_GLIBC_PACKAGE_VERSION/$ALPINE_GLIBC_I18N_PACKAGE_FILENAME" && \
    apk add --no-cache \
        "$ALPINE_GLIBC_BASE_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_BIN_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_I18N_PACKAGE_FILENAME" && \
    \
    rm "/etc/apk/keys/sgerrand.rsa.pub" && \
    /usr/glibc-compat/bin/localedef --force --inputfile POSIX --charmap UTF-8 C.UTF-8 || true && \
    echo "export LANG=C.UTF-8" > /etc/profile.d/locale.sh && \
    \
    apk del glibc-i18n wget && \
    \
    rm "/root/.wget-hsts" && \
    apk del .build-dependencies && \
    rm \
        "$ALPINE_GLIBC_BASE_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_BIN_PACKAGE_FILENAME" \
        "$ALPINE_GLIBC_I18N_PACKAGE_FILENAME"   

COPY src/dist/proxy-listener /usr/local/bin
RUN chmod +x /usr/local/bin/proxy-listener && \
    mkdir -p /var/log/float

EXPOSE 80/tcp 443/tcp

STOPSIGNAL SIGQUIT

ENTRYPOINT [ "/docker-entrypoint.sh" ]
CMD [ "-g", "daemon off;" ]
